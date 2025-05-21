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
            this.parent.innerHTML = response.html;

            this.init(this.parent.querySelector('.js-sydatagrid')!);
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

        orderDescSelect?.addEventListener('change', () => {
            const direction = orderDescSelect.value as 'asc' | 'desc';
            const column = orderIn?.value;

            if (direction && column) {
                this.pdata.filters = {
                    ...this.pdata.filters,
                    order: { [column]: direction }
                };
            }
        });

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
}