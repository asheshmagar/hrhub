import { useQuery } from '@tanstack/react-query';
import { format } from 'date-fns';
import { CalendarIcon } from 'lucide-react';
import React from 'react';
import { FormProvider, UseFormReturn } from 'react-hook-form';
import { Button } from '../../../../@/components/ui/button';
import { Calendar } from '../../../../@/components/ui/calendar';
import {
	Form as ReactForm,
	FormControl,
	FormField,
	FormItem,
	FormLabel,
	FormMessage,
} from '../../../../@/components/ui/form';
import { Input } from '../../../../@/components/ui/input';
import { PhoneInput } from '../../../../@/components/ui/phone-input';
import {
	Popover,
	PopoverContent,
	PopoverTrigger,
} from '../../../../@/components/ui/popover';
import { ReactSelect } from '../../../../@/components/ui/react-select';
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from '../../../../@/components/ui/select';
import { Api } from '../../../../@/lib/api';
import { cn } from '../../../../@/lib/utils';
import {
	DepartmentSchema,
	EmployeeSchema,
	PositionSchema,
} from '../../../types/schema';

type Props = {
	form: UseFormReturn<EmployeeSchema>;
	onSubmit: (data: EmployeeSchema) => void;
	isLoading?: boolean;
	submitBtnText: string;
};

export const Form = ({ form, onSubmit, isLoading, submitBtnText }: Props) => {
	const departmentApi = new Api('hrhub/v1/departments');
	const positionApi = new Api('hrhub/v1/positions');
	const departmentsQuery = useQuery({
		queryKey: ['departments'],
		queryFn: () =>
			departmentApi.list<{
				departments: Array<
					DepartmentSchema & {
						id: number;
						employees: Array<any>;
					}
				>;
			}>({ per_page: 99 }),
	});
	const positionsQuery = useQuery({
		queryKey: ['positions'],
		queryFn: () =>
			positionApi.list<{
				positions: Array<
					PositionSchema & {
						id: number;
						employees: Array<any>;
					}
				>;
			}>({ per_page: 99 }),
	});

	const departmentOptions = departmentsQuery.data?.departments.map((d) => ({
		value: d.id,
		label: d.name,
	}));
	const positionOptions = positionsQuery.data?.positions.map((d) => ({
		value: d.id,
		label: d.name,
	}));
	return (
		<FormProvider {...form}>
			<ReactForm {...form}>
				<div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
					<FormField
						control={form.control}
						name="name"
						rules={{
							required: true,
						}}
						render={({ field }) => (
							<FormItem>
								<FormLabel>Name</FormLabel>
								<FormControl>
									<Input placeholder="Enter name" {...field} />
								</FormControl>
								{form.formState.errors.name && (
									<FormMessage>
										{form.formState.errors.name.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="email"
						rules={{
							required: true,
						}}
						render={({ field }) => (
							<FormItem>
								<FormLabel>Email</FormLabel>
								<FormControl>
									<Input type="email" placeholder="Enter email" {...field} />
								</FormControl>
								{form.formState.errors.email && (
									<FormMessage>
										{form.formState.errors.email.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="phone_number"
						rules={{
							required: true,
						}}
						render={({ field }) => (
							<FormItem>
								<FormLabel>Phone number</FormLabel>
								<FormControl>
									<PhoneInput defaultCountry="NP" {...field} />
								</FormControl>
								{form.formState.errors.phone_number && (
									<FormMessage>
										{form.formState.errors.phone_number.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="date_of_employment"
						rules={{
							required: true,
						}}
						render={({ field }) => (
							<FormItem className="flex flex-col">
								<FormLabel>Date of employment</FormLabel>
								<Popover>
									<PopoverTrigger asChild>
										<FormControl>
											<Button
												variant={'outline'}
												className={cn(
													'w-full pl-3 text-left font-normal',
													!field.value && 'text-muted-foreground',
												)}
											>
												{field.value ? (
													format(field.value, 'PPP')
												) : (
													<span>Pick a date</span>
												)}
												<CalendarIcon className="ml-auto h-4 w-4 opacity-50" />
											</Button>
										</FormControl>
									</PopoverTrigger>
									<PopoverContent className="w-auto p-0" align="start">
										<Calendar
											mode="single"
											selected={field.value ? new Date(field.value) : undefined}
											onSelect={field.onChange}
											disabled={(date) => date > new Date()}
											initialFocus
										/>
									</PopoverContent>
								</Popover>
								{form.formState.errors.date_of_employment && (
									<FormMessage>
										{form.formState.errors.date_of_employment.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="date_of_birth"
						rules={{
							required: true,
						}}
						render={({ field }) => (
							<FormItem className="flex flex-col">
								<FormLabel>Date of birth</FormLabel>
								<Popover>
									<PopoverTrigger asChild>
										<FormControl>
											<Button
												variant={'outline'}
												className={cn(
													'w-full pl-3 text-left font-normal',
													!field.value && 'text-muted-foreground',
												)}
											>
												{field.value ? (
													format(field.value, 'PPP')
												) : (
													<span>Pick a date</span>
												)}
												<CalendarIcon className="ml-auto h-4 w-4 opacity-50" />
											</Button>
										</FormControl>
									</PopoverTrigger>
									<PopoverContent className="w-auto p-0" align="start">
										<Calendar
											mode="single"
											selected={field.value ? new Date(field.value) : undefined}
											onSelect={field.onChange}
											disabled={(date) => date > new Date()}
											initialFocus
										/>
									</PopoverContent>
								</Popover>
								{form.formState.errors.date_of_birth && (
									<FormMessage>
										{form.formState.errors.date_of_birth.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="address"
						rules={{
							required: true,
						}}
						render={({ field }) => (
							<FormItem>
								<FormLabel>Address</FormLabel>
								<FormControl>
									<Input {...field} />
								</FormControl>
								{form.formState.errors.address && (
									<FormMessage>
										{form.formState.errors.address.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="status"
						rules={{
							required: true,
						}}
						render={({ field }) => (
							<FormItem>
								<FormLabel>Status</FormLabel>
								<FormControl>
									<Select onValueChange={field.onChange} value={field.value}>
										<SelectTrigger className="w-full">
											<SelectValue placeholder="Select status" />
										</SelectTrigger>
										<SelectContent>
											<SelectItem value="inactive">Inactive</SelectItem>
											<SelectItem value="active">Active</SelectItem>
											<SelectItem value="terminated">Terminated</SelectItem>
										</SelectContent>
									</Select>
								</FormControl>
								{form.formState.errors.status && (
									<FormMessage>
										{form.formState.errors.status.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="department"
						render={({ field }) => (
							<FormItem>
								<FormLabel>Department</FormLabel>
								<FormControl>
									<ReactSelect
										createAble={false}
										options={departmentOptions ?? []}
										defaultValue={departmentOptions?.find(
											(d) => d.value === field.value,
										)}
										isLoading={departmentsQuery.isLoading}
										placeholder="Select department"
										onInputChange={(v) => {}}
										onChange={(v) => field.onChange(v?.value ?? undefined)}
									/>
								</FormControl>
								{form.formState.errors.department && (
									<FormMessage>
										{form.formState.errors.department.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="position"
						render={({ field }) => (
							<FormItem>
								<FormLabel>Position</FormLabel>
								<FormControl>
									<ReactSelect
										createAble={false}
										options={positionOptions ?? []}
										defaultValue={positionOptions?.find(
											(d) => d.value === field.value,
										)}
										isLoading={positionsQuery.isLoading}
										placeholder="Select position"
										onInputChange={(v) => {}}
										onChange={(v) => field.onChange(v?.value ?? undefined)}
									/>
								</FormControl>
								{form.formState.errors.department && (
									<FormMessage>
										{form.formState.errors.department.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
				</div>
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
