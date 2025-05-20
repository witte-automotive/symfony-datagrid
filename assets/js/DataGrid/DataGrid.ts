import Ajax from "../Service/Ajax"

interface IPaginated {
    visiblePages: number[]
    totalPages: number
    total: number
    page: number
    perPage: number
    pageRange: number
}

export default class DataGrid {
    pdata!: IPaginated;
    container: HTMLElement;
    url: string;

    public init = (container: HTMLElement) => {
        if (!container) return;
        this.container = container;
        this.pdata = JSON.parse(container.dataset.paginationData!);
        this.url = container.dataset.resetUrl!;
        this.bindPaginationEvents();
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
            this.container.innerHTML = response.html;

            this.init(this.container);
        } catch (error) {
            console.error("Pagination request failed:", error);
        }
    }
}