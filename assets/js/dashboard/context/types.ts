export interface User {
	id: number;
	username: string;
	name: string;
	first_name: string;
	last_name: string;
	email: string;
	url: string;
	description: string;
	link: string;
	locale: string;
	nickname: string;
	slug: string;
	roles: string[];
	registered_date: string;
	capabilities: Record<string, boolean>;
	extra_capabilities: {
		administrator: boolean;
	};
	avatar_urls: {
		'24': string;
		'48': string;
		'96': string;
	};
	meta: Record<any, any>;
	_links: {
		self: {
			href: string;
		}[];
		collection: {
			href: string;
		}[];
	};
}
