import { useMutation, useQueryClient } from '@tanstack/react-query';
import React from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { useToast } from '../../../@/components/ui/use-toast';
import { Api } from '../../../@/lib/api';
import { EmployeeSchema } from '../../types/schema';
import { Form } from './components/Form';

export const NewEmployee = () => {
	const form = useForm<EmployeeSchema>();
	const api = new Api('hrhub/v1/employees');
	const { toast } = useToast();
	const queryClient = useQueryClient();
	const navigate = useNavigate();

	const employeeMutation = useMutation({
		mutationFn: (data: EmployeeSchema) => api.create(data),
		onSuccess() {
			queryClient.invalidateQueries({ queryKey: ['employees'] });
			toast({
				title: 'Employee created successfully',
			});
			navigate('/employees');
		},
		onError(e: Error) {
			toast({
				title: 'Failed to create employee',
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
			<Form
				form={form}
				onSubmit={onSubmit}
				submitBtnText="Create"
				isLoading={employeeMutation.isPending}
			/>
		</div>
	);
};
