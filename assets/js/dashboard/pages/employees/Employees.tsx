import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { CircleAlertIcon, Plus } from 'lucide-react';
import React from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { ListTable } from '../../../@/components/list-table';
import { buttonVariants } from '../../../@/components/ui/button';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { Api } from '../../../@/lib/api';
import { cn } from '../../../@/lib/utils';
import { EmployeesSchema } from '../../types/types';
import { columns } from './components/columns';

import { TableSkeleton } from '../../../@/components/skeleton/table';
import {
	Alert,
	AlertDescription,
	AlertTitle,
} from '../../../@/components/ui/alert';

export const Employees = () => {
	const api = new Api('hrhub/v1/employees');
	const [searchParams, setSearchParams] = useSearchParams();
	const queryClient = useQueryClient();
	const employeesQuery = useQuery({
		queryKey: ['employees'],
		queryFn: () =>
			api.list<{
				data: any[];
				total: number;
				pages: number;
			}>({
				per_page: searchParams.get('limit') ?? undefined,
				page: searchParams.get('page') ?? undefined,
				search: searchParams.get('search') ?? undefined,
			}),
	});

	const employeesMutation = useMutation({
		mutationFn: (data: { per_page?: string; page?: string; search?: string }) =>
			api.list<EmployeesSchema>(data),
		onSuccess(data) {
			queryClient.setQueryData(['employees'], data);
		},
	});

	const onSearchParamsChange = (v: Record<string, string>) => {
		for (const key in v) {
			if (v[key] === '') {
				searchParams.delete(key);
			} else {
				searchParams.set(key, v[key]);
			}
		}
		setSearchParams(searchParams);
		employeesMutation.mutate({
			per_page: searchParams.get('limit') ?? undefined,
			page: searchParams.get('page') ?? undefined,
			search: searchParams.get('search') ?? undefined,
		});
	};

	return (
		<div className="flex-1 space-y-4  p-4 md:p-8 pt-6">
			<BreadCrumb items={[{ title: 'Employees', link: '/employees' }]} />
			<div className="flex items-start justify-between">
				<Heading
					title={`Employees (${employeesQuery.data?.total ?? 0})`}
					description="Manage employees"
				/>

				<Link
					to={'/employees/new'}
					className={cn(buttonVariants({ variant: 'default' }))}
				>
					<Plus className="mr-2 h-4 w-4" /> Add New
				</Link>
			</div>
			<Separator />
			{employeesQuery.isLoading ? (
				<TableSkeleton
					rows={5}
					columns={['NAME', 'DEPARTMENT', 'POSITION', 'PHONE', 'STATUS']}
				/>
			) : employeesQuery.isError ? (
				<Alert variant="destructive">
					<CircleAlertIcon className="h-4 w-4" />
					<AlertTitle>Failed to fetch data!</AlertTitle>
					<AlertDescription>
						Message:{' '}
						{employeesQuery.error?.message ?? 'An unknown error occurred.'}
					</AlertDescription>
				</Alert>
			) : (
				<ListTable
					columns={columns}
					data={
						employeesQuery.data?.data?.map((e) => ({
							id: e.id,
							name: e.name,
							department: e.department?.name ?? '(Unassigned)',
							position: e.position?.name ?? '(Unassigned)',
							phone_number: e.phone_number,
							status: e.status,
						})) ?? []
					}
					pages={employeesQuery.data?.pages ?? -1}
					total={employeesQuery.data?.total ?? 0}
					onQuery={onSearchParamsChange}
					page={searchParams.get('page') ?? '1'}
					limit={searchParams.get('limit') ?? '10'}
					search={searchParams.get('search') ?? ''}
					loading={employeesQuery.isLoading || employeesMutation.isPending}
				/>
			)}
		</div>
	);
};
