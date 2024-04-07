import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import React, { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import { useNavigate, useParams } from 'react-router-dom';
import BreadCrumb from '../../../@/components/breadcrumbs';
import { Heading } from '../../../@/components/ui/heading';
import { Separator } from '../../../@/components/ui/separator';
import { useToast } from '../../../@/components/ui/use-toast';
import { Api } from '../../../@/lib/api';
import { PositionSchema } from '../../types/schema';
import { Form } from './components/Form';

export const EditPosition = () => {
	const { toast } = useToast();
	const { positionId } = useParams();
	const form = useForm<PositionSchema>();
	const positionApi = new Api('hrhub/v1/positions');
	const { data, isLoading } = useQuery({
		queryKey: ['position', `position:${positionId}`],
		queryFn: () =>
			positionApi.get<PositionSchema>(parseInt(positionId as string)),
	});
	const queryClient = useQueryClient();
	const navigate = useNavigate();

	useEffect(() => {
		if (data && !form.formState.isDirty) {
			form.reset(data);
		}
	}, [data]); // eslint-disable-line react-hooks/exhaustive-deps

	const positionMutation = useMutation({
		mutationFn: (data: PositionSchema) =>
			positionApi.update(parseInt(positionId as string), data),
		onSuccess() {
			queryClient.invalidateQueries({ queryKey: ['positions'] });
			toast({
				title: 'Position updated successfully',
			});
			navigate('/positions');
		},
		onError(e: Error) {
			toast({
				title: 'Failed to update position',
				description: e.message,
				variant: 'destructive',
			});
		},
	});

	const onSubmit = (data: PositionSchema) => {
		positionMutation.mutate(data);
	};

	return (
		<div className="flex-1 space-y-4 p-8">
			<BreadCrumb
				items={[
					{ title: 'Positions', link: '/positions' },
					{ title: 'Create', link: '/positions/new' },
				]}
			/>
			<div className="flex items-center justify-between">
				<Heading title={'Create position'} description={'Add a new position'} />
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
