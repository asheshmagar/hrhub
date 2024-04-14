'use client';
import { ColumnDef } from '@tanstack/react-table';
import React from 'react';
import { Link } from 'react-router-dom';
import { Checkbox } from '../../../../@/components/ui/checkbox';

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
				<Link to={`/positions/${row.original.id}/edit`}>
					{row.getValue('name')}
				</Link>
			);
		},
	},
	{
		accessorKey: 'description',
		header: 'DESCRIPTION',
		cell: ({ row }) => {
			return <p className="max-w-[300px]">{row.getValue('description')}</p>;
		},
	},
	{
		accessorKey: 'employees',
		header: 'EMPLOYEES',
	},
	{
		id: 'actions',
		cell: ({ row }) => <span>:</span>,
	},
];
