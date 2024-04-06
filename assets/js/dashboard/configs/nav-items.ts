import {
	CircleArrowOutUpLeftIcon,
	FileStackIcon,
	Grid2X2Icon,
	ListCollapseIcon,
	StarsIcon,
	UnfoldVerticalIcon,
	UsersRoundIcon,
} from 'lucide-react';

export const navItems = [
	{
		name: 'Overview',
		to: '/',
		icon: ListCollapseIcon,
	},
	{
		name: 'Employees',
		to: '/employees',
		icon: UsersRoundIcon,
	},
	{
		name: 'Departments',
		to: '/departments',
		icon: Grid2X2Icon,
	},
	{
		name: 'Positions',
		to: '/positions',
		icon: UnfoldVerticalIcon,
	},
	{
		name: 'Leaves',
		to: '/leaves',
		icon: CircleArrowOutUpLeftIcon,
	},
	{
		name: 'Reviews',
		to: '/Reviews',
		icon: StarsIcon,
	},
	{
		name: 'Attendances',
		to: '/attendances',
		icon: FileStackIcon,
	},
];
