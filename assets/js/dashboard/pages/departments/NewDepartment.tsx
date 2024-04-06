import { useMutation, useQueryClient } from '@tanstack/react-query';
import React from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { useToast } from '../../../@/components/ui/use-toast';
import { Api } from '../../../@/lib/api';
import { DepartmentSchema } from '../../types/schema';
import { Form } from './components/Form';

export const NewDepartment = () => {
	const form = useForm<DepartmentSchema>();
	const api = new Api('hrhub/v1/departments');
	const { toast } = useToast();
	const queryClient = useQueryClient();
	const navigate = useNavigate();

	const employeeMutation = useMutation({
		mutationFn: (data: DepartmentSchema) => api.create(data),
		onSuccess() {
			queryClient.invalidateQueries({ queryKey: ['departments'] });
			toast({
				title: 'Department created successfully',
			});
			navigate('/departments');
		},
		onError(e: Error) {
			toast({
				title: 'Failed to create department',
				description: e.message,
				variant: 'destructive',
			});
		},
	});

	const onSubmit = (data: DepartmentSchema) => {
		employeeMutation.mutate(data);
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
			<Form form={form} onSubmit={onSubmit} submitBtnText="Create" />
		</div>
	);
};
