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
        this.initSortable();
        this.toggleLoader(false);
        this.initFilter();
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

            this.toggleLoader(true)
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
        } finally {
            this.toggleLoader(false)
        }
    }

    private initSortable = () => {
        new Sortable(this.container.querySelector('tbody')!, {
            animation: 150,
            handle: '.js-sdg-sort-handle',
            onEnd: async (evt) => {
                const rows = evt.target.children;

                const ids: string[] = Array.from(rows)
                    .map(row => (row as HTMLElement).dataset.id)
                    .filter((id): id is string => id !== undefined);

                this.pdata.sorted = ids;
                await this.fetchPageData();
                this.pdata.sorted = []
                await this.fetchPageData();
            },
        });
    }

    private initFilter = () => {
        const sortBtns = this.container.querySelectorAll<HTMLElement>('.js-sydatagrid-col-sort')

        sortBtns.forEach(btn => {
            const col = btn.dataset.col!;
            const dir = btn.querySelector<HTMLElement>('.js-sydatagrid-col-sort-icon-placeholder')?.dataset.sortDir === 'desc' ? 'asc' : 'desc'

            btn.addEventListener('click', () => {
                this.pdata.filters.order = { [col]: dir };
                this.fetchPageData();
            })
        })
    }
}