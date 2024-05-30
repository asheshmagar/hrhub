import { useQueries } from '@tanstack/react-query';
import { Users2Icon } from 'lucide-react';
import React from 'react';
import {
	Card,
	CardContent,
	CardDescription,
	CardHeader,
	CardTitle,
} from '../../../@/components/ui/card';
import { Skeleton } from '../../../@/components/ui/skeleton';
import { Api } from '../../../@/lib/api';
import { DepartmentsOverview } from './components/DepartmentsOverview';

export const Overview = () => {
	const salariedEmployeeApi = new Api('hrhub/v1/analytics/employees/salary');
	const employeesByDepartment = new Api(
		'hrhub/v1/analytics/employees/department',
	);
	const employeesByPosition = new Api('hrhub/v1/analytics/employees/position');

	const queries = useQueries({
		queries: [
			{
				queryKey: ['employee-salary'],
				queryFn: () => salariedEmployeeApi.list(),
			},
			{
				queryKey: ['employee-position'],
				queryFn: () => employeesByPosition.list(),
			},
			{
				queryKey: ['employee-department'],
				queryFn: () => employeesByDepartment.list(),
			},
		],
	});

	const isLoading = queries.some((r) => r.isLoading);
	let totalEmployees = 0;
	if (!isLoading) {
		totalEmployees = [].concat(
			...(Object.values((queries[0].data as any).data) as Array<any>),
		).length;
	}

	console.log(queries[2]);

	return (
		<div className="flex-1 space-y-4 p-4 md:p-8 pt-6">
			<div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
				<Card>
					<CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
						<CardTitle className="text-sm font-medium">
							Total Employees
						</CardTitle>
						<Users2Icon className="h-4 w-4 text-muted-foreground" />
					</CardHeader>
					<CardContent>
						{isLoading ? (
							<Skeleton className="h-[32px] w-[14px] border-none" />
						) : (
							<div className="text-2xl font-bold">{totalEmployees}</div>
						)}
					</CardContent>
				</Card>
				<Card>
					<CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
						<CardTitle className="text-sm font-medium">Subscriptions</CardTitle>
						<svg
							xmlns="http://www.w3.org/2000/svg"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							strokeLinecap="round"
							strokeLinejoin="round"
							strokeWidth="2"
							className="h-4 w-4 text-muted-foreground"
						>
							<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
							<circle cx="9" cy="7" r="4" />
							<path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
						</svg>
					</CardHeader>
					<CardContent>
						<div className="text-2xl font-bold">+2350</div>
						<p className="text-xs text-muted-foreground">
							+180.1% from last month
						</p>
					</CardContent>
				</Card>
				<Card>
					<CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
						<CardTitle className="text-sm font-medium">Sales</CardTitle>
						<svg
							xmlns="http://www.w3.org/2000/svg"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							strokeLinecap="round"
							strokeLinejoin="round"
							strokeWidth="2"
							className="h-4 w-4 text-muted-foreground"
						>
							<rect width="20" height="14" x="2" y="5" rx="2" />
							<path d="M2 10h20" />
						</svg>
					</CardHeader>
					<CardContent>
						<div className="text-2xl font-bold">+12,234</div>
						<p className="text-xs text-muted-foreground">
							+19% from last month
						</p>
					</CardContent>
				</Card>
				<Card>
					<CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
						<CardTitle className="text-sm font-medium">Active Now</CardTitle>
						<svg
							xmlns="http://www.w3.org/2000/svg"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							strokeLinecap="round"
							strokeLinejoin="round"
							strokeWidth="2"
							className="h-4 w-4 text-muted-foreground"
						>
							<path d="M22 12h-4l-3 9L9 3l-3 9H2" />
						</svg>
					</CardHeader>
					<CardContent>
						<div className="text-2xl font-bold">+573</div>
						<p className="text-xs text-muted-foreground">
							+201 since last hour
						</p>
					</CardContent>
				</Card>
			</div>
			<div className="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-7">
				<Card className="col-span-4">
					<CardHeader>
						<CardTitle>Overview</CardTitle>
					</CardHeader>
					<CardContent className="pl-2">
						{isLoading ? (
							<Skeleton className="h-[32px] w-[14px] border-none" />
						) : (
							<DepartmentsOverview
								data={(queries[2].data as any).data?.map((d: any) => ({
									name: d.name,
									employees: (d.employees as Array<any>).length,
								}))}
							/>
						)}
					</CardContent>
				</Card>
				<Card className="col-span-4 md:col-span-3">
					<CardHeader>
						<CardTitle>Recent Sales</CardTitle>
						<CardDescription>You made 265 sales this month.</CardDescription>
					</CardHeader>
					<CardContent></CardContent>
				</Card>
			</div>
		</div>
	);
};
