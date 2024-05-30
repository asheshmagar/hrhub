import { __ } from '@wordpress/i18n';
import React from 'react';
import { FormProvider, UseFormReturn } from 'react-hook-form';
import { Button } from '../../../../@/components/ui/button';
import {
	Form as ReactForm,
	FormControl,
	FormField,
	FormItem,
	FormLabel,
	FormMessage,
} from '../../../../@/components/ui/form';
import { Input } from '../../../../@/components/ui/input';
import { Textarea } from '../../../../@/components/ui/textarea';
import { PositionSchema } from '../../../types/schema';

type Props = {
	form: UseFormReturn<PositionSchema & { id?: number }>;
	onSubmit: (
		data: PositionSchema & {
			id?: number;
		},
	) => void;
	isLoading?: boolean;
	submitBtnText: string;
};

export const Form = ({ form, onSubmit, isLoading, submitBtnText }: Props) => {
	return (
		<FormProvider {...form}>
			<ReactForm {...form}>
				<FormField
					control={form.control}
					name="name"
					render={({ field }) => (
						<FormItem>
							<FormLabel>{__('Name', 'hrhub')}</FormLabel>
							<FormControl>
								<Input placeholder="Enter name" {...field} />
							</FormControl>
							{form.formState.errors.name && (
								<FormMessage>{form.formState.errors.name.message}</FormMessage>
							)}
						</FormItem>
					)}
				/>
				<FormField
					control={form.control}
					name="description"
					render={({ field }) => (
						<FormItem>
							<FormLabel>Description</FormLabel>
							<FormControl>
								<Textarea {...field} />
							</FormControl>
							{form.formState.errors.description && (
								<FormMessage>
									{form.formState.errors.description.message}
								</FormMessage>
							)}
						</FormItem>
					)}
				/>
				<input {...form.register('id')} hidden />
				<Button
					type="submit"
					onClick={form.handleSubmit(onSubmit)}
					loading={isLoading}
					disabled={!form.formState.isDirty}
				>
					{submitBtnText}
				</Button>
			</ReactForm>
		</FormProvider>
	);
};
