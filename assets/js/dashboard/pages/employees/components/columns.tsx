'use client';
import { ColumnDef } from '@tanstack/react-table';
import React from 'react';
import { Link } from 'react-router-dom';
import { Checkbox } from '../../../../@/components/ui/checkbox';
import { TableCellAction } from './TableCellAction';

export const columns: ColumnDef<any>[] = [
	{
		id: 'select',
		header: ({ table }) => (
			<Checkbox
				checked={table.getIsAllPageRowsSelected()}
				onCheckedChange={(value) => table.toggleAllPageRowsSelected(!!value)}
				aria-label="Select all"
			/>
		),
		cell: ({ row }) => (
			<Checkbox
				checked={row.getIsSelected()}
				onCheckedChange={(value) => row.toggleSelected(!!value)}
				aria-label="Select row"
			/>
		),
		enableSorting: false,
		enableHiding: false,
	},
	{
		accessorKey: 'name',
		header: 'NAME',
		cell: ({ row }) => {
			return (
				<Link to={`/employees/${row.original.id}/edit`}>
					{row.getValue('name')}
				</Link>
			);
		},
	},
	{
		accessorKey: 'department',
		header: 'DEPARTMENT',
	},
	{
		accessorKey: 'position',
		header: 'POSITION',
	},
	{
		accessorKey: 'phone_number',
		header: 'PHONE',
	},
	{
		accessorKey: 'status',
		header: 'STATUS',
	},
	{
		id: 'actions',
		cell: ({ row }) => <TableCellAction data={row.original} />,
	},
];
