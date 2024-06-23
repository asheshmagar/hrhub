import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { useParams } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { useToast } from '../../../@/components/ui/use-toast';
import { Api } from '../../../@/lib/api';
import { EmployeeSchema } from '../../types/schema';
import { Form } from './components/Form';

export const EditEmployee = () => {
	const { employeeId } = useParams();
	const employeeApi = new Api('hrhub/v1/employees');
	const { data, isLoading } = useQuery({
		queryKey: ['employee', `employee:${employeeId}`],
		queryFn: () => employeeApi.get(parseInt(employeeId as string)),
	});
	const { toast } = useToast();
	const [loading, setLoading] = useState(false);

	const form = useForm<EmployeeSchema>({
		defaultValues: async () => {
			setLoading(true);
			const res = await employeeApi.get<
				EmployeeSchema & {
					department: any;
					position: any;
				}
			>(parseInt(employeeId as string));

			return new Promise((resolve) => {
				resolve({
					...res,
					position: res.position?.id,
					department: res.department?.id,
				});
				setLoading(false);
			});
		},
	});

	// useEffect(() => {
	// 	if (data && !form.formState.isDirty) {
	// 		form.reset({
	// 			...data,
	// 			// @ts-expect-error
	// 			department: data?.department?.id
	// 				? // @ts-expect-error
	// 					Number(data.department.id)
	// 				: undefined,
	// 			// @ts-expect-error
	// 			position: data?.position?.id ? Number(data.position.id) : undefined,
	// 		});
	// 	}
	// }, [data]); // eslint-disable-line

	const api = new Api('hrhub/v1/employees');
	const queryClient = useQueryClient();

	const employeeMutation = useMutation({
		mutationFn: (data: any) => api.update(parseInt(employeeId as string), data),
		onSuccess() {
			toast({
				title: 'Employee updated successfully',
			});
			queryClient.invalidateQueries({ queryKey: ['employees'] });
		},
		onError(e: Error) {
			toast({
				title: 'Failed to update employee',
				description: e.message,
				variant: 'destructive',
			});
		},
	});
	const onSubmit = (data: EmployeeSchema) => {
		employeeMutation.mutate(data);
	};

	return (
		<div className="flex-1 space-y-4 p-8">
			<BreadCrumb
				items={[
					{ title: 'Employees', link: '/employees' },
					{ title: 'Create', link: '/employees/new' },
				]}
			/>
			<div className="flex items-center justify-between">
				<Heading title={'Create employee'} description={'Add a new employee'} />
			</div>
			<Separator />
			{isLoading ? (
				<div>Loading...</div>
			) : (
				<Form
					form={form}
					onSubmit={onSubmit}
					submitBtnText="Update"
					isLoading={employeeMutation.isPending}
				/>
			)}
		</div>
	);
};
