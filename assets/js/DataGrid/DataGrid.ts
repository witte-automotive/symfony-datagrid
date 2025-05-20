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

    }
}