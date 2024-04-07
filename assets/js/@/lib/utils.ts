import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
	return twMerge(clsx(inputs));
}

export function trailingslashit(url: string) {
	return url.endsWith('/') ? url : url + '/';
}

export const file2blob = (file: File) => {
	return new Promise((resolve, reject) => {
		const reader = new FileReader();
		reader.readAsArrayBuffer(file);
		reader.onload = () => resolve(reader.result);
		reader.onerror = (error) => reject(error);
	});
};

export const files2formData = (files: File[]) => {
	const formData = new FormData();
	for (let i = 0; i < files.length; i++) {
		formData.append(`documents.${i}`, files[i]);
	}
	return formData;
};
