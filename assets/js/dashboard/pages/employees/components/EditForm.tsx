import { useMutation, useQueryClient } from '@tanstack/react-query';
import { format } from 'date-fns';
import { CalendarIcon } from 'lucide-react';
import React from 'react';
import { FormProvider, useForm } from 'react-hook-form';
import { useNavigate } from 'react-router-dom';
import { Button } from '../../../../@/components/ui/button';
import { Calendar } from '../../../../@/components/ui/calendar';
import {
	Form,
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
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from '../../../../@/components/ui/select';
import { useToast } from '../../../../@/components/ui/use-toast';
import { Api } from '../../../../@/lib/api';
import { cn } from '../../../../@/lib/utils';
import { EmployeeSchema } from '../../../types/schema';

export const EditForm = (props: { data: Record<string, any> }) => {
	const { toast } = useToast();

	const form = useForm<EmployeeSchema>({
		defaultValues: props.data,
	});
	const api = new Api('hrhub/v1/employees');
	const navigate = useNavigate();
	const queryClient = useQueryClient();

	const employeeMutation = useMutation({
		mutationFn: (data: any) => api.update(props.data.id, data),
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
		<FormProvider {...form}>
			<Form {...form}>
				<FormField
					control={form.control}
					name="name"
					render={({ field }) => (
						<FormItem>
							<FormLabel>Name</FormLabel>
							<FormControl>
								<Input placeholder="Enter name" {...field} />
							</FormControl>
							<FormMessage />
						</FormItem>
					)}
				/>
				<FormField
					control={form.control}
					name="email"
					render={({ field }) => (
						<FormItem>
							<FormLabel>Email</FormLabel>
							<FormControl>
								<Input type="email" placeholder="Enter email" {...field} />
							</FormControl>
							<FormMessage />
						</FormItem>
					)}
				/>
				<FormField
					control={form.control}
					name="phone_number"
					render={({ field }) => (
						<FormItem>
							<FormLabel>Phone number</FormLabel>
							<FormControl>
								<PhoneInput defaultCountry="NP" {...field} />
							</FormControl>
							<FormMessage />
						</FormItem>
					)}
				/>
				<FormField
					control={form.control}
					name="date_of_employment"
					render={({ field }) => (
						<FormItem className="flex flex-col">
							<FormLabel>Date of birth</FormLabel>
							<Popover>
								<PopoverTrigger asChild>
									<FormControl>
										<Button
											variant={'outline'}
											className={cn(
												'w-[240px] pl-3 text-left font-normal',
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
							<FormMessage />
						</FormItem>
					)}
				/>
				<FormField
					control={form.control}
					name="date_of_birth"
					render={({ field }) => (
						<FormItem className="flex flex-col">
							<FormLabel>Date of birth</FormLabel>
							<Popover>
								<PopoverTrigger asChild>
									<FormControl>
										<Button
											variant={'outline'}
											className={cn(
												'w-[240px] pl-3 text-left font-normal',
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
							<FormMessage />
						</FormItem>
					)}
				/>
				<FormField
					control={form.control}
					name="address"
					render={({ field }) => (
						<FormItem>
							<FormLabel>Address</FormLabel>
							<FormControl>
								<Input {...field} />
							</FormControl>
							<FormMessage />
						</FormItem>
					)}
				/>
				<FormField
					control={form.control}
					name="status"
					render={({ field }) => (
						<FormItem>
							<FormLabel>Status</FormLabel>
							<FormControl>
								<Select
									onValueChange={field.onChange}
									defaultValue={field.value}
								>
									<SelectTrigger className="w-[240px]">
										<SelectValue placeholder="Select status" />
									</SelectTrigger>
									<SelectContent>
										<SelectItem value="inactive">Inactive</SelectItem>
										<SelectItem value="active">Active</SelectItem>
										<SelectItem value="terminated">Terminated</SelectItem>
									</SelectContent>
								</Select>
							</FormControl>
							<FormMessage />
						</FormItem>
					)}
				/>
				<Button
					type="submit"
					onClick={form.handleSubmit(onSubmit)}
					loading={employeeMutation.isPending}
				>
					Submit
				</Button>
			</Form>
		</FormProvider>
	);
};
