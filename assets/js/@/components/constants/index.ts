import { NavItem } from '../../types';

export const navItems: NavItem[] = [
	{
		title: 'Dashboard',
		href: '/',
		icon: 'dashboard',
		label: 'Dashboard',
		roles: ['administrator', 'hrhub_manager', 'hrhub_employee'],
	},
	{
		title: 'Employees',
		href: '/employees',
		icon: 'employee',
		label: 'Employees',
		roles: ['administrator', 'hrhub_manager'],
	},
	{
		title: 'Departments',
		href: '/departments',
		icon: 'kanban',
		label: 'Departments',
		roles: ['administrator', 'hrhub_manager'],
	},
	{
		title: 'Positions',
		href: '/positions',
		icon: 'laptop',
		label: 'Positions',
		roles: ['administrator', 'hrhub_manager'],
	},
	{
		title: 'Leaves',
		href: '/leaves',
		icon: 'arrowRight',
		label: 'Leaves',
		roles: ['administrator', 'hrhub_manager', 'hrhub_employee'],
	},
	{
		title: 'Reviews',
		href: '/reviews',
		icon: 'stars',
		label: 'Reviews',
		roles: ['administrator', 'hrhub_manager', 'hrhub_employee'],
	},
];

export const ALLOWED_ROLES = [
	'administrator',
	'hrhub_manager',
	'hrhub_employee',
];
