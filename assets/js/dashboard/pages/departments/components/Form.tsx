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
import { DepartmentSchema } from '../../../types/schema';

type Props = {
	form: UseFormReturn<DepartmentSchema>;
	onSubmit: (data: DepartmentSchema) => void;
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
							<FormLabel>Name</FormLabel>
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
