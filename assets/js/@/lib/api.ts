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

	async list<T>(q?: Record<string, any>) {
		return apiFetch<T>({
			path: this.#path + '?' + queryString.stringify(q ?? {}),
			method: ApiMethod.READABLE,
		});
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

	async get<T>(id: number) {
		return apiFetch<T>({
			path: this.#path + id,
			method: ApiMethod.READABLE,
		});
	}
}
