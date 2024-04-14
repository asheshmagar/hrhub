'use client';
import { MenuIcon } from 'lucide-react';
import React, { useContext, useState } from 'react';
import { UserContext } from '../../../dashboard/context/UserContext';
import { navItems } from '../constants';
import { DashboardNav } from '../dashboard-nav';
import { Sheet, SheetContent, SheetTrigger } from '../ui/sheet';

interface SidebarProps extends React.HTMLAttributes<HTMLDivElement> {}

export function MobileSidebar({ className }: SidebarProps) {
	const [open, setOpen] = useState(false);
	const user = useContext(UserContext);
	const menuItems = navItems.filter((item) =>
		item.roles.some((r) => user?.data?.roles.includes(r)),
	);
	return (
		<>
			<Sheet open={open} onOpenChange={setOpen}>
				<SheetTrigger asChild>
					<MenuIcon />
				</SheetTrigger>
				<SheetContent side="left" className="!px-0">
					<div className="space-y-4 py-4">
						<div className="px-3 py-2">
							<h2 className="mb-2 px-4 text-lg font-semibold tracking-tight">
								Overview
							</h2>
							<div className="space-y-1">
								<DashboardNav items={menuItems} setOpen={setOpen} />
							</div>
						</div>
					</div>
				</SheetContent>
			</Sheet>
		</>
	);
}
