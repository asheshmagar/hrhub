import apiFetch from '@wordpress/api-fetch';
import queryString from 'query-string';
import { trailingslashit } from './utils';

export enum ApiMethod {
	READABLE = 'GET',
	CREATABLE = 'POST',
	DELETABLE = 'DELETE',
}

export class Api {
	#path: string;

	constructor(path: string) {
		this.#path = trailingslashit(path);
	}

	async list<T, E = Error>(q?: Record<string, any>) {
		try {
			const res = await apiFetch<Response>({
				path: this.#path + '?' + queryString.stringify(q ?? {}),
				method: ApiMethod.READABLE,
				parse: false,
			});

			if (!(res.status >= 200 && res.status < 300)) {
				throw res;
			}

			const total = res.headers.get('X-WP-Total');
			const pages = res.headers.get('X-WP-TotalPages');
			const data = await res.json();

			return {
				data,
				total: total ? parseInt(total) : undefined,
				pages: pages ? parseInt(pages) : undefined,
			} as T;
		} catch (error) {
			throw error as E;
		}
	}

	async create<T extends {}, R>(data: T) {
		return apiFetch<R>({
			path: this.#path,
			method: ApiMethod.CREATABLE,
			data: data,
		});
	}

	async update<T extends {}, R>(id: number, data: T) {
		return apiFetch<R>({
			path: this.#path + id,
			method: ApiMethod.CREATABLE,
			data: data,
			headers: {
				'x-http-method-override': 'PUT',
			},
		});
	}

	async delete<T>(id: number) {
		return apiFetch<T>({
			path: this.#path + id,
			method: ApiMethod.CREATABLE,
			headers: {
				'x-http-method-override': 'DELETE',
			},
		});
	}

	async get<T>(id: number, context: 'edit' | 'embed' | 'view' = 'view') {
		return apiFetch<T>({
			path: this.#path + id + '?context=' + context,
			method: ApiMethod.READABLE,
		});
	}
}
