import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import React, { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate, useParams } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { useToast } from '../../../@/components/ui/use-toast';
import { Api } from '../../../@/lib/api';
import { DepartmentSchema } from '../../types/schema';
import { Form } from './components/Form';

export const EditDepartment = () => {
	const { toast } = useToast();
	const { departmentId } = useParams();
	const form = useForm<DepartmentSchema>();
	const departmentApi = new Api('hrhub/v1/departments');
	const { data, isLoading } = useQuery({
		queryKey: ['department', `department:${departmentId}`],
		queryFn: () =>
			departmentApi.get<DepartmentSchema>(parseInt(departmentId as string)),
	});
	const queryClient = useQueryClient();
	const navigate = useNavigate();

	useEffect(() => {
		if (data && !form.formState.isDirty) {
			form.reset(data);
		}
	}, [data]); // eslint-disable-line react-hooks/exhaustive-deps

	const departmentMutation = useMutation({
		mutationFn: (data: DepartmentSchema) =>
			departmentApi.update(parseInt(departmentId as string), data),
		onSuccess() {
			queryClient.invalidateQueries({ queryKey: ['departments'] });
			toast({
				title: 'Department updated successfully',
			});
			navigate('/departments');
		},
		onError(e: Error) {
			toast({
				title: 'Failed to update department',
				description: e.message,
				variant: 'destructive',
			});
		},
	});

	const onSubmit = (data: DepartmentSchema) => {
		departmentMutation.mutate(data);
	};

	return (
		<div className="flex-1 space-y-4 p-8">
			<BreadCrumb
				items={[
					{ title: 'Departments', link: '/departments' },
					{ title: 'Create', link: '/departments/new' },
				]}
			/>
			<div className="flex items-center justify-between">
				<Heading
					title={'Create department'}
					description={'Add a new department'}
				/>
			</div>
			<Separator />
			{isLoading ? (
				<p>Loading...</p>
			) : (
				<Form form={form} onSubmit={onSubmit} submitBtnText="Update" />
			)}
		</div>
	);
};
