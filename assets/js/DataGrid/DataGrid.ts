import Ajax from "../Service/Ajax"
import Sortable from 'sortablejs';

interface IPaginated {
    visiblePages: number[];
    totalPages: number;
    total: number;
    page: number;
    perPage: number;
    pageRange: number;
    filters: {
        search?: { [key: string]: string }[];
        order?: { [column: string]: 'asc' | 'desc' };
    };
    sorted: string[]
}

export default class DataGrid {
    pdata!: IPaginated;
    container: HTMLElement;
    url: string;
    parent: HTMLElement;

    public init = (container: HTMLElement) => {
        if (!container) return;
        this.container = container;
        this.parent = container.parentElement!;
        this.pdata = JSON.parse(container.dataset.paginationData!);
        this.url = container.dataset.resetUrl!;
        this.bindPaginationEvents();
        this.initFilters();
        this.initSortable();
        this.toggleLoader(false);
    }

    private bindPaginationEvents = (): void => {
        const addClickListener = (selector: string, handler: () => void) => {
            const btn = this.container.querySelector(selector);
            if (btn) {
                btn.addEventListener('click', handler);
            }
        };

        this.container.querySelectorAll<HTMLButtonElement>('.js-sdg-p-page').forEach(btn => {
            btn.addEventListener('click', () => {
                const page = Number(btn.dataset.page);
                if (!isNaN(page)) {
                    this.updateCurrentPage(page);
                }
            });
        });

        addClickListener('.js-sdg-p-prev-btn', () => this.updateCurrentPage(this.pdata.page - 1));
        addClickListener('.js-sdg-p-next-btn', () => this.updateCurrentPage(this.pdata.page + 1));
        addClickListener('.js-sdg-p-fp', () => this.updateCurrentPage(1));
        addClickListener('.js-sdg-p-lp', () => this.updateCurrentPage(this.pdata.totalPages));

        this.initPerPageChange();
    }

    private toggleLoader = (on: boolean = true) => {
        if (on) {
            this.container.querySelector('.js-sydatagrid-loader')!.classList.remove('!hidden');
            this.container.querySelector('.js-sydatagrid-table-container')!.classList.add('hidden');
        } else {
            this.container.querySelector('.js-sydatagrid-loader')!.classList.add('!hidden');
            this.container.querySelector('.js-sydatagrid-table-container')!.classList.remove('hidden');
        }
    }

    private initPerPageChange = () => {
        const perPageSelect = this.container.querySelector<HTMLInputElement>('.js-sdg-pps');
        perPageSelect?.addEventListener('change', () => {
            this.pdata.perPage = Number(perPageSelect.value);
            this.fetchPageData();
        })
    }

    private updateCurrentPage = (page: number): void => {
        if (page < 1 || page > this.pdata.totalPages) return;
        this.pdata.page = page;
        this.fetchPageData();
    }

    private fetchPageData = async (): Promise<void> => {
        try {
            const { totalPages, visiblePages, ...rest } = this.pdata;
            this.pdata = rest as any;

            const response = await Ajax.get<{ pagination: IPaginated } & { html: string }>(this.url, {
                params: this.pdata,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            this.container.setAttribute('data-pagination-data', JSON.stringify(response.pagination));

            const parsedDoc = new DOMParser().parseFromString(response.html, 'text/html');
            const newGrid = parsedDoc.querySelector('.js-sydatagrid');

            if (newGrid) {
                const oldGrid = this.parent.querySelector('.js-sydatagrid');
                if (oldGrid) {
                    this.parent.replaceChild(newGrid, oldGrid);
                }

                this.init(newGrid as HTMLElement);
            }
        } catch (error) {
            console.error("Pagination request failed:", error);
        }
    }

    private initFilters = () => {
        const container = this.container.querySelector('.js-sdg-f');
        if (!container) return;

        const openBtn = this.container.querySelector('.js-sydatagrid-f-display-btn');
        const cancelBtn = this.container.querySelector('.js-sdg-f-cancle-btn');
        const resetBtn = this.container.querySelector('.js-sdg-f-s-r-btn');

        openBtn?.addEventListener('click', () => {
            container.classList.remove('hidden');
            container.classList.add('flex');
        });

        cancelBtn?.addEventListener('click', () => {
            container.classList.remove('flex');
            container.classList.add('hidden');
        });

        const searchItemTemplate = container.querySelector<HTMLTemplateElement>('.js-sdg-f-s-inp-item-template')?.content;
        const searchItemsContainer = container.querySelector<HTMLElement>('.js-sdg-f-s-inps-cont');
        const searchItemAddBtn = container.querySelector<HTMLButtonElement>('.js-sdg-f-s-btn');

        const orderDescSelect = container.querySelector<HTMLSelectElement>('.js-sdg-s-order-desc');
        const orderIn = container.querySelector<HTMLInputElement>('.js-sdg-s-order-in');

        const updateOrderFilter = () => {
            const direction = orderDescSelect?.value as 'asc' | 'desc';
            const column = orderIn?.value;
            if (direction && column) {
                this.pdata.filters = {
                    ...this.pdata.filters,
                    order: { [column]: direction }
                };
            }
        };

        orderDescSelect?.addEventListener('change', updateOrderFilter);
        orderIn?.addEventListener('change', updateOrderFilter);

        resetBtn?.addEventListener('click', () => {
            searchItemsContainer!.innerHTML = ''
            addSearchItem();
        })

        if (!searchItemTemplate || !searchItemsContainer) return;

        const addSearchItem = (column = '', query = '') => {
            const newItem = searchItemTemplate.cloneNode(true) as HTMLElement;
            const queryInput = newItem.querySelector<HTMLInputElement>('.js-sdg-f-s-inp-query');
            const columnSelect = newItem.querySelector<HTMLSelectElement>('.js-sdg-f-s-inp-in');

            if (queryInput) queryInput.value = query;
            if (columnSelect) columnSelect.value = column;

            searchItemsContainer.appendChild(newItem);
        };

        if (Array.isArray(this.pdata.filters?.search) && this.pdata.filters.search.length > 0) {
            for (const entry of this.pdata.filters.search) {
                const column = Object.keys(entry)[0];
                const query = entry[column];
                addSearchItem(column, query);
            }
        } else {
            addSearchItem();
        }

        searchItemAddBtn?.addEventListener('click', () => {
            addSearchItem();
        });

        const form = container.querySelector<HTMLFormElement>('.js-sdg-f-form');
        form?.addEventListener('submit', (event) => {
            event.preventDefault();

            const searchInputs = searchItemsContainer.querySelectorAll<HTMLInputElement>('.js-sdg-f-s-inp-query');
            const columnSelects = searchItemsContainer.querySelectorAll<HTMLSelectElement>('.js-sdg-f-s-inp-in');

            const search: { [key: string]: string }[] = [];

            if (searchInputs.length === columnSelects.length) {
                for (let i = 0; i < searchInputs.length; i++) {
                    const query = searchInputs[i].value.trim();
                    const column = columnSelects[i].value;
                    if (query && column) {
                        search.push({ [column]: query });
                    }
                }
            }

            this.pdata.filters = { ...this.pdata.filters, search: search };
            this.pdata.page = 1;
            this.fetchPageData();
        });
    };

    private initSortable = () => {
        new Sortable(this.container.querySelector('tbody')!, {
            animation: 150,
            handle: '.js-sdg-sort-handle',
            onEnd: async (evt) => {
                const rows = evt.target.children;

                const ids: string[] = Array.from(rows)
                    .map(row => (row as HTMLElement).dataset.id)
                    .filter((id): id is string => id !== undefined);

                const updatedId = evt.item.dataset.id

                this.pdata.sorted = ids;
                this.toggleLoader(true)
                await this.fetchPageData();
                this.pdata.sorted = []
                await this.fetchPageData();
                this.toggleLoader(false)
            },
        });
    }
}