import { useMutation, useQueryClient } from '@tanstack/react-query';
import { __ } from '@wordpress/i18n';
import React from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { useToast } from '../../../@/components/ui/use-toast';
import { Api } from '../../../@/lib/api';
import { PositionSchema } from '../../types/schema';
import { Form } from './components/Form';

export const NewPosition = () => {
	const form = useForm<PositionSchema>();
	const api = new Api('hrhub/v1/positions');
	const { toast } = useToast();
	const queryClient = useQueryClient();
	const navigate = useNavigate();

	const employeeMutation = useMutation({
		mutationFn: (data: PositionSchema) => api.create(data),
		onSuccess() {
			queryClient.invalidateQueries({ queryKey: ['positions'] });
			toast({
				title: 'Position created successfully',
			});
			navigate('/positions');
		},
		onError(e: Error) {
			toast({
				title: 'Failed to create position',
				description: e.message,
				variant: 'destructive',
			});
		},
	});

	const onSubmit = (data: PositionSchema) => {
		employeeMutation.mutate(data);
	};

	return (
		<div className="flex-1 space-y-4 p-8">
			<BreadCrumb
				items={[
					{ title: __('Positions', 'hrhub'), link: '/positions' },
					{ title: __('Create', 'hrhub'), link: '/positions/new' },
				]}
			/>
			<div className="flex items-center justify-between">
				<Heading
					title={'Create position'}
					description={__('Add a new position', 'hrhub')}
				/>
			</div>
			<Separator />
			<Form
				form={form}
				onSubmit={onSubmit}
				submitBtnText={__('Create', 'hrhub')}
			/>
		</div>
	);
};
