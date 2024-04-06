import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Edit, MoreHorizontal, Trash } from 'lucide-react';
import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { AlertModal } from '../../../../@/components/alert-modal';
import { Button } from '../../../../@/components/ui/button';
import {
	DropdownMenu,
	DropdownMenuContent,
	DropdownMenuItem,
	DropdownMenuLabel,
	DropdownMenuTrigger,
} from '../../../../@/components/ui/dropdown-menu';
import { Api } from '../../../../@/lib/api';

export const TableCellAction = (props: { data: Record<string, any> }) => {
	const EmployeesApi = new Api('hrhub/v1/employees');
	const queryClient = useQueryClient();
	const deleteEmployee = useMutation({
		mutationFn: () => EmployeesApi.delete(props.data.id),
		onSuccess() {
			queryClient.invalidateQueries({ queryKey: ['employees'] });
		},
	});
	const [isOpen, setIsOpen] = useState(false);
	return (
		<>
			<DropdownMenu modal={false}>
				<DropdownMenuTrigger asChild>
					<Button variant="ghost" className="h-8 w-8 p-0">
						<span className="sr-only">Open menu</span>
						<MoreHorizontal className="h-4 w-4" />
					</Button>
				</DropdownMenuTrigger>
				<DropdownMenuContent align="end">
					<DropdownMenuLabel>Actions</DropdownMenuLabel>
					<DropdownMenuItem asChild>
						<Link to={`/employees/${props.data.id}/edit`}>
							<Edit className="mr-2 h-4 w-4" /> Edit
						</Link>
					</DropdownMenuItem>
					<DropdownMenuItem onClick={() => setIsOpen(true)}>
						<Trash className="mr-2 h-4 w-4" /> Delete
					</DropdownMenuItem>
				</DropdownMenuContent>
			</DropdownMenu>
			<AlertModal
				isOpen={isOpen}
				onClose={() => setIsOpen(false)}
				onConfirm={async () => {
					await deleteEmployee.mutateAsync();
					setIsOpen(false);
				}}
				loading={deleteEmployee.isPending}
				title={`Deleting employee #${props.data.id}`}
				description={`Are you sure you want to delete employee #${props.data.id}?`}
			/>
		</>
	);
};
