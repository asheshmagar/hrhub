import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Plus } from 'lucide-react';
import React from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { ListTable } from '../../../@/components/list-table';
import { buttonVariants } from '../../../@/components/ui/button';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { Api } from '../../../@/lib/api';
import { cn } from '../../../@/lib/utils';
import { DepartmentSchema } from '../../types/schema';
import { EmployeesSchema } from '../../types/types';
import { columns } from './components/columns';

type Department = {
	current: number;
	departments: Array<DepartmentSchema & { id: number }>;
	pages: number;
	total: number;
};

export const Departments = () => {
	const api = new Api('hrhub/v1/departments');
	const [searchParams, setSearchParams] = useSearchParams();
	const queryClient = useQueryClient();
	const departmentsQuery = useQuery({
		queryKey: ['departments'],
		queryFn: () =>
			api.list<Department>({
				per_page: searchParams.get('limit') ?? undefined,
				page: searchParams.get('page') ?? undefined,
				search: searchParams.get('search') ?? undefined,
			}),
	});

	const departmentsMutation = useMutation({
		mutationFn: (data: { per_page?: string; page?: string; search?: string }) =>
			api.list<EmployeesSchema>(data),
		onSuccess(data) {
			queryClient.setQueryData(['departments'], data);
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
		departmentsMutation.mutate({
			per_page: searchParams.get('limit') ?? undefined,
			page: searchParams.get('page') ?? undefined,
			search: searchParams.get('search') ?? undefined,
		});
	};

	return (
		<div className="flex-1 space-y-4  p-4 md:p-8 pt-6">
			<BreadCrumb items={[{ title: 'Departments', link: '/departments' }]} />
			<div className="flex items-start justify-between">
				<Heading
					title={`Departments (${departmentsQuery?.data?.total ?? 0})`}
					description="Manage departments"
				/>

				<Link
					to={'/departments/new'}
					className={cn(buttonVariants({ variant: 'default' }))}
				>
					<Plus className="mr-2 h-4 w-4" /> Add New
				</Link>
			</div>
			<Separator />
			{departmentsQuery.isLoading ? (
				<p>Loading...</p>
			) : (
				<ListTable
					columns={columns}
					data={
						departmentsQuery.data?.departments.map((e) => ({
							id: e.id,
							name: e.name,
							description: e.description ?? '(No description)',
						})) ?? []
					}
					pages={departmentsQuery.data?.pages ?? -1}
					total={departmentsQuery.data?.total ?? 0}
					onQuery={onSearchParamsChange}
					page={searchParams.get('page') ?? '1'}
					limit={searchParams.get('limit') ?? '10'}
					search={searchParams.get('search') ?? ''}
					loading={departmentsQuery.isLoading || departmentsMutation.isPending}
				/>
			)}
		</div>
	);
};
