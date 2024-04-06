import {
	ColumnDef,
	flexRender,
	getCoreRowModel,
	getFilteredRowModel,
	getPaginationRowModel,
	useReactTable,
} from '@tanstack/react-table';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-react';
import React from 'react';
import { Button } from './ui/button';
import { Input } from './ui/input';
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from './ui/select';
import {
	Table,
	TableBody,
	TableCell,
	TableHead,
	TableHeader,
	TableRow,
} from './ui/table';

import {
	DoubleArrowLeftIcon,
	DoubleArrowRightIcon,
} from '@radix-ui/react-icons';
import { useDebouncedCallback } from 'use-debounce';
import { cn } from '../lib/utils';
import { ScrollArea, ScrollBar } from './ui/scroll-area';

interface ListTableProps<TData, TValue> {
	columns: ColumnDef<TData, TValue>[];
	data: TData[];
	pages: number;
	total: number;
	search?: string;
	onQuery?: (args: { page?: string; search?: string; limit?: string }) => void;
	page?: string;
	limit?: string;
	loading?: boolean;
}

export const ListTable = <TData, TValue>(
	props: ListTableProps<TData, TValue>,
) => {
	const debouncedOnSearch = useDebouncedCallback((v: string) => {
		props?.onQuery?.({
			search: v,
		});
	}, 400);

	const table = useReactTable({
		data: props.data,
		columns: props.columns,
		pageCount: props.pages,
		getCoreRowModel: getCoreRowModel(),
		getFilteredRowModel: getFilteredRowModel(),
		getPaginationRowModel: getPaginationRowModel(),
		manualPagination: true,
		manualFiltering: true,
		state: {
			pagination: {
				pageIndex: parseInt(props?.page ?? '1') - 1,
				pageSize: parseInt(props.limit ?? '10'),
			},
		},
	});

	const onPageChange = (page: number) => {
		props?.onQuery?.({
			page: page > 1 ? page.toString() : '',
		});
	};

	return (
		<>
			<Input
				placeholder={`Search ${props.search ?? ''}...`}
				defaultValue={props.search}
				onChange={(event) => debouncedOnSearch(event.target.value)}
				className="w-full md:max-w-sm"
			/>

			<ScrollArea
				className={cn('rounded-md border h-[calc(80vh-220px)]', {
					'pointer-events-none opacity-50': props.loading,
				})}
			>
				<Table className="relative">
					<TableHeader>
						{table.getHeaderGroups().map((headerGroup) => (
							<TableRow key={headerGroup.id}>
								{headerGroup.headers.map((header) => {
									return (
										<TableHead key={header.id}>
											{header.isPlaceholder
												? null
												: flexRender(
														header.column.columnDef.header,
														header.getContext(),
													)}
										</TableHead>
									);
								})}
							</TableRow>
						))}
					</TableHeader>
					<TableBody>
						{table.getRowModel().rows?.length ? (
							table.getRowModel().rows.map((row) => (
								<TableRow
									key={row.id}
									data-state={row.getIsSelected() && 'selected'}
								>
									{row.getVisibleCells().map((cell) => (
										<TableCell key={cell.id}>
											{flexRender(
												cell.column.columnDef.cell,
												cell.getContext(),
											)}
										</TableCell>
									))}
								</TableRow>
							))
						) : (
							<TableRow>
								<TableCell
									colSpan={props.columns.length}
									className="h-24 text-center"
								>
									No results.
								</TableCell>
							</TableRow>
						)}
					</TableBody>
				</Table>
				<ScrollBar orientation="horizontal" />
			</ScrollArea>
			<div className="flex flex-col gap-2 sm:flex-row items-center justify-end space-x-2 py-4">
				<div className="flex items-center justify-between w-full">
					<div className="flex-1 text-sm text-muted-foreground">
						{table.getFilteredSelectedRowModel().rows.length} of{' '}
						{table.getFilteredRowModel().rows.length} row(s) selected.
					</div>
					<div className="flex flex-col items-center gap-4 sm:flex-row sm:gap-6 lg:gap-8">
						<div className="flex items-center space-x-2">
							<p className="whitespace-nowrap text-sm font-medium">
								Rows per page
							</p>
							<Select
								value={`${table.getState().pagination.pageSize}`}
								onValueChange={(value) => {
									table.setPageSize(Number(value));
									props.onQuery?.({
										limit: '10' !== value ? value : '',
									});
								}}
							>
								<SelectTrigger className="h-8 w-[70px]">
									<SelectValue
										placeholder={table.getState().pagination.pageSize}
									/>
								</SelectTrigger>
								<SelectContent side="top">
									{[10, 20, 30, 40, 50].map((pageSize) => (
										<SelectItem key={pageSize} value={`${pageSize}`}>
											{pageSize}
										</SelectItem>
									))}
								</SelectContent>
							</Select>
						</div>
					</div>
				</div>
				<div className="flex items-center justify-between sm:justify-end gap-2 w-full">
					<div className="flex w-[100px] items-center justify-center text-sm font-medium">
						Page {table.getState().pagination.pageIndex + 1} of{' '}
						{table.getPageCount()}
					</div>
					<div className="flex items-center space-x-2">
						<Button
							aria-label="Go to first page"
							variant="outline"
							className="hidden h-8 w-8 p-0 lg:flex"
							onClick={() => {
								table.setPageIndex(0);
								onPageChange(1);
							}}
							disabled={!table.getCanPreviousPage()}
						>
							<DoubleArrowLeftIcon className="h-4 w-4" aria-hidden="true" />
						</Button>
						<Button
							aria-label="Go to previous page"
							variant="outline"
							className="h-8 w-8 p-0"
							onClick={() => {
								table.previousPage();
								onPageChange(table.getState().pagination.pageIndex + 1 - 1);
							}}
							disabled={!table.getCanPreviousPage()}
						>
							<ChevronLeftIcon className="h-4 w-4" aria-hidden="true" />
						</Button>
						<Button
							aria-label="Go to next page"
							variant="outline"
							className="h-8 w-8 p-0"
							onClick={() => {
								table.nextPage();
								onPageChange(table.getState().pagination.pageIndex + 2);
							}}
							disabled={!table.getCanNextPage()}
						>
							<ChevronRightIcon className="h-4 w-4" aria-hidden="true" />
						</Button>
						<Button
							aria-label="Go to last page"
							variant="outline"
							className="hidden h-8 w-8 p-0 lg:flex"
							onClick={() => {
								table.setPageIndex(table.getPageCount() - 1);
								onPageChange(table.getState().pagination.pageIndex + 2);
							}}
							disabled={!table.getCanNextPage()}
						>
							<DoubleArrowRightIcon className="h-4 w-4" aria-hidden="true" />
						</Button>
					</div>
				</div>
			</div>
		</>
	);
};
