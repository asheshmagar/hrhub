import React, { useContext } from 'react';
import { UserContext } from '../../../dashboard/context/UserContext';
import { navItems } from '../constants';
import { DashboardNav } from '../dashboard-nav';

export default function Sidebar() {
	const user = useContext(UserContext);
	const menuItems = navItems.filter((item) =>
		item.roles.some((r) => user?.data?.roles.includes(r)),
	);
	return (
		<nav className="relative hidden h-screen border-r pt-16 lg:block w-72">
			<div className="space-y-4 py-4">
				<div className="px-3 py-2">
					<div className="space-y-1">
						<h2 className="mb-2 px-4 text-xl font-semibold tracking-tight">
							Overview
						</h2>
						<DashboardNav items={menuItems} />
					</div>
				</div>
			</div>
		</nav>
	);
}
