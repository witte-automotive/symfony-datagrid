import axios, { AxiosRequestConfig, AxiosResponse, AxiosError } from 'axios';

enum ResponseStatus {
    ERROR = 0,
    SUCCESS = 1,
}

export interface Response<T> {
    success: ResponseStatus;
    data: T;
}

interface IRequestOptions<T = any> {
    headers?: Record<string, string>;
    params?: Record<string, any>;
    data?: T;
}

interface IErrorResponse {
    status?: number;
    message: string;
    data?: any;
}

export default class Ajax {
    private static handleError(error: unknown): IErrorResponse {
        if (axios.isAxiosError(error)) {
            return {
                status: error.response?.status,
                message: error.message,
                data: error.response?.data,
            };
        }
        return {
            message: (error as Error).message || 'Unknown error occurred',
        };
    }

    private static async request<T>(
        method: 'GET' | 'POST',
        url: string,
        options: IRequestOptions = {}
    ): Promise<T> {
        const headers: Record<string, string> = { ...options.headers };
        let data: any = undefined;

        if (method === 'POST' && options.data) {
            if (options.data instanceof FormData) {
                data = options.data;
            } else {
                data = JSON.stringify(options.data);
                headers['Content-Type'] = headers['Content-Type'] || 'application/json';
            }
        }

        const config: AxiosRequestConfig = {
            method,
            url,
            headers,
            params: options.params,
            data,
        };

        try {
            const response: AxiosResponse<T> = await axios(config);
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    }

    static get<T>(url: string, options: IRequestOptions = {}): Promise<T> {
        return this.request<T>('GET', url, options);
    }

    static post<T>(url: string, options: IRequestOptions = {}): Promise<T> {
        return this.request<T>('POST', url, options);
    }
}
