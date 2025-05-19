enum ColumnTypes {
    DATE = 'date',
    DATETIME = 'datetime'
}

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

    public init = (it: HTMLElement) => {
        const pdata = JSON.parse(it.dataset.paginationData!) as IPaginated
        this.pdata = pdata;

        const container = document.querySelector<HTMLElement>('.js-sydatagrid');
        if (!container) return;

        const pcontainer = container.querySelector<HTMLTemplateElement>('.js-sdg-p-template')?.content
        if (!pcontainer) return;

        this.updatePagination(container, pcontainer);
    }

    private updatePagination = (container: HTMLElement, pcontainer: DocumentFragment) => {
        const prevBtn = pcontainer.querySelector<HTMLElement>('.js-sdg-p-prev-btn')!;
        const nextBtn = pcontainer.querySelector<HTMLElement>('.js-sdg-p-next-btn')!;

        this.initPrevNextBtns(prevBtn, nextBtn);
        container.querySelector('.js-sydatagrid-p-placeholder')!.appendChild(pcontainer);
    }

    private initPrevNextBtns = (prev: HTMLElement, next: HTMLElement) => {
        if (this.pdata.page == 1) {
            prev.classList.add('text-gray-200', 'cursor-not-allowed', 'pointer-events-none')
        }else{
            
        }

    }
}
// {{ paginated.page == 1 ? 'text-gray-200 cursor-not-allowed pointer-events-none' : 'text-dark hover:bg-light' }}
//  {{ paginated.page == paginated.totalPages ? 'text-gray-200 cursor-not-allowed pointer-events-none' : 'text-gray-700 hover:bg-light' }}