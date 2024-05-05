import { DevTool } from '@hookform/devtools';
import { useQuery } from '@tanstack/react-query';
import { MediaUpload } from '@wordpress/media-utils';
import { format } from 'date-fns';
import { CalendarIcon, Eye, FileIcon, Image, Plus, X } from 'lucide-react';
import React, { useState } from 'react';
import { FormProvider, UseFormReturn } from 'react-hook-form';
import { Link } from 'react-router-dom';
import { DocumentPreview } from '../../../../@/components/document-preview';
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
import { ReactAsyncSelect } from '../../../../@/components/ui/react-select';
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from '../../../../@/components/ui/select';
import { Api } from '../../../../@/lib/api';
import { cn, debounce } from '../../../../@/lib/utils';
import {
	AttachmentSchema,
	DepartmentSchema,
	EmployeeSchema,
	PositionSchema,
} from '../../../types/schema';

type Props = {
	form: UseFormReturn<EmployeeSchema>;
	onSubmit: (data: EmployeeSchema) => void;
	isLoading?: boolean;
	submitBtnText: string;
	isEdit?: boolean;
};

export const Form = ({ form, onSubmit, isLoading, submitBtnText }: Props) => {
	const departmentApi = new Api('hrhub/v1/departments');
	const positionApi = new Api('hrhub/v1/positions');
	const departmentsQuery = useQuery({
		queryKey: ['departments'],
		queryFn: () =>
			departmentApi.list<{
				data: Array<
					DepartmentSchema & {
						id: number;
						employees: Array<any>;
					}
				>;
			}>({ per_page: 10 }),
	});
	const positionsQuery = useQuery({
		queryKey: ['positions'],
		queryFn: () =>
			positionApi.list<{
				data: Array<
					PositionSchema & {
						id: number;
						employees: Array<any>;
					}
				>;
			}>({ per_page: 10 }),
	});
	const [preview, setPreview] = useState<{
		url: string;
		type: string;
	} | null>(null);

	const departmentOptions = departmentsQuery.data?.data.map((d) => ({
		value: d.id,
		label: d.name,
	}));

	const positionOptions = positionsQuery.data?.data.map((d) => ({
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
									<PhoneInput
										placeholder="980-0000000"
										defaultCountry="NP"
										{...field}
									/>
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
											captionLayout="dropdown-buttons"
											fromYear={1990}
											toYear={new Date().getFullYear()}
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
											initialFocus
											captionLayout="dropdown-buttons"
											fromYear={1800}
											toYear={new Date().getFullYear() + 10}
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
									<Input placeholder="Enter address" {...field} />
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
									<ReactAsyncSelect
										isClearable
										onChange={(v: any) => {
											field.onChange(v.value);
										}}
										defaultOptions={
											departmentsQuery.isSuccess
												? departmentsQuery.data.data.map((d) => ({
														label: d.name,
														value: d.id,
													}))
												: []
										}
										placeholder={'Select department'}
										value={departmentOptions?.find(
											(d) => d.value === field.value,
										)}
										loadOptions={debounce((searchValue, callback) => {
											if (searchValue.length < 0) {
												return callback([]);
											}
											departmentApi
												.list<{
													data: Array<
														DepartmentSchema & {
															id: number;
															employees: Array<any>;
														}
													>;
												}>({ search: searchValue })
												.then((data) => {
													callback(
														data?.data?.map((d) => {
															return {
																value: d.id,
																label: d.name,
															};
														}),
													);
												});
										})}
										isLoading={departmentsQuery.isLoading}
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
									<ReactAsyncSelect
										isClearable
										onChange={(v: any) => {
											field.onChange(v.value);
										}}
										defaultOptions={
											positionsQuery.isSuccess
												? positionsQuery.data.data.map((d) => ({
														label: d.name,
														value: d.id,
													}))
												: []
										}
										placeholder={'Select position'}
										value={positionOptions?.find(
											(d) => d.value === field.value,
										)}
										loadOptions={debounce((searchValue, callback) => {
											if (searchValue.length < 0) {
												return callback([]);
											}
											positionApi
												.list<{
													data: Array<
														PositionSchema & {
															id: number;
															employees: Array<any>;
														}
													>;
												}>({ search: searchValue })
												.then((data) => {
													callback(
														data?.data?.map((d) => {
															return {
																value: d.id,
																label: d.name,
															};
														}),
													);
												});
										})}
										isLoading={positionsQuery.isLoading}
									/>
								</FormControl>
								{form.formState.errors.position && (
									<FormMessage>
										{form.formState.errors.position.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="salary"
						render={({ field }) => (
							<FormItem>
								<FormLabel>Salary</FormLabel>
								<FormControl>
									<Input placeholder="00.00" type="number" {...field} />
								</FormControl>
								{form.formState.errors.salary && (
									<FormMessage>
										{form.formState.errors.salary.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<FormField
						control={form.control}
						name="employment_type"
						rules={{
							required: true,
						}}
						render={({ field }) => (
							<FormItem>
								<FormLabel>Employment type</FormLabel>
								<FormControl>
									<Select onValueChange={field.onChange} value={field.value}>
										<SelectTrigger className="w-full">
											<SelectValue placeholder="Select employment type" />
										</SelectTrigger>
										<SelectContent>
											<SelectItem value="full-time">Full time</SelectItem>
											<SelectItem value="part-time">Part time</SelectItem>
											<SelectItem value="trainee/intern">
												Trainee / Intern
											</SelectItem>
											<SelectItem value="contractor/freelancer">
												Contractor / Freelancer
											</SelectItem>
										</SelectContent>
									</Select>
								</FormControl>
								{form.formState.errors.employment_type && (
									<FormMessage>
										{form.formState.errors.employment_type.message}
									</FormMessage>
								)}
							</FormItem>
						)}
					/>
					<div className="col-span-1 sm:col-span-2 md:col-span-3">
						<FormField
							control={form.control}
							name="documents"
							render={({ field }) => (
								<FormItem className="space-y-4">
									<FormLabel>Documents</FormLabel>
									<FormControl>
										<>
											<MediaUpload
												value={field.value?.map((v) => v.id)}
												render={({ open }) => {
													return (
														<div>
															<Button variant="outline" onClick={open}>
																<Plus className="w-4 h-4" />
																Add
															</Button>
														</div>
													);
												}}
												onSelect={(attachments: AttachmentSchema[]) => {
													const nextValue = field.value ?? [];
													for (const attachment of attachments) {
														const index = field.value?.findIndex(
															(v) => v.id === attachment.id,
														);
														if (undefined !== index && -1 !== index) {
															nextValue.splice(index, 1, attachment);
															continue;
														}
														nextValue.push(attachment);
													}
													field.onChange(nextValue);
												}}
												multiple
												allowedTypes={['image', 'application/pdf']}
											/>
											<div className="p-4 mt-8 border border-dashed border-gray-300 rounded-md">
												{!field.value ? (
													<p>No documents</p>
												) : (
													<div className="flex flex-col gap-2">
														{field.value.map((file) => (
															<div
																key={file.id}
																className="flex border border-gray-300 gap-4 p-4 rounded-md items-center justify-between"
															>
																<div className="flex items-center gap-4">
																	{file.type.startsWith('image') ? (
																		<Image />
																	) : (
																		<FileIcon />
																	)}
																	<span>
																		{file.title + ` (${file.filename})`}
																	</span>
																	<Button asChild variant="link">
																		<Link
																			to={`/documents/preview/${file.id}`}
																		></Link>
																		<Button
																			variant="link"
																			onClick={() => {
																				setPreview({
																					url: file.url,
																					type:
																						file.mime === 'application/pdf'
																							? 'pdf'
																							: 'image',
																				});
																			}}
																			className="p-0"
																		>
																			<Eye className="h-4 w-4" />
																			<span className="sr-only">
																				Preview document {file.title}
																			</span>
																		</Button>
																	</Button>
																</div>
																<Button
																	variant="ghost"
																	className="h-5 w-5 p-0"
																	onClick={() => {
																		field.onChange(
																			field.value?.filter(
																				(f) => f.id !== file.id,
																			),
																		);
																	}}
																>
																	<X />
																</Button>
															</div>
														))}
													</div>
												)}
											</div>
										</>
									</FormControl>
									{form.formState.errors.documents && (
										<FormMessage>
											{form.formState.errors.documents.message}
										</FormMessage>
									)}
								</FormItem>
							)}
						/>
					</div>
				</div>
				<Input type="hidden" {...form.register('id')} />
				<Button
					type="submit"
					onClick={form.handleSubmit(onSubmit)}
					loading={isLoading}
					disabled={!form.formState.isDirty}
				>
					{submitBtnText}
				</Button>
			</ReactForm>
			<DocumentPreview
				preview={preview}
				onClose={() => {
					setPreview(null);
				}}
			/>
			<DevTool control={form.control} />
		</FormProvider>
	);
};
