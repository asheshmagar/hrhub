import React, { Dispatch, SetStateAction } from 'react';
import { NavLink as Link } from 'react-router-dom';
import { cn } from '../lib/utils';
import { NavItem } from '../types';
import { Icons } from './icons';

interface DashboardNavProps {
	items: NavItem[];
	setOpen?: Dispatch<SetStateAction<boolean>>;
}

export function DashboardNav({ items, setOpen }: DashboardNavProps) {
	if (!items?.length) {
		return null;
	}
	return (
		<nav className="grid items-start gap-2">
			{items.map((item, index) => {
				const Icon = Icons[item.icon || 'arrowRight'];
				return (
					item.href && (
						<Link
							key={index}
							to={item.disabled ? '/' : item.href}
							onClick={() => {
								if (setOpen) setOpen(false);
							}}
							className={({ isActive }) => {
								return cn(
									'group flex items-center rounded-md px-3 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground',
									isActive ? 'bg-accent' : 'transparent',
									item.disabled && 'cursor-not-allowed opacity-80',
								);
							}}
						>
							<Icon className="mr-2 h-4 w-4" />
							<span>{item.title}</span>
						</Link>
					)
				);
			})}
		</nav>
	);
}
