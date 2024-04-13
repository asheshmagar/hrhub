import React from 'react';
import { Skeleton } from '../ui/skeleton';

type Props = {
	columns: Array<string>;
	rows?: number;
};

export const TableSkeleton = ({ columns }: Props) => {
	return (
		<div className="space-y-4">
			<Skeleton className="h-[40px] w-[386px]" />
			<table className="w-full caption-bottom text-sm relative">
				<thead className="[&_tr]:border-b">
					<tr className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
						{columns.map((c, i) => (
							<th
								className="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0"
								key={i}
							>
								{c}
							</th>
						))}
					</tr>
				</thead>
				<tbody className="[&_tr:last-child]:border-0">
					{Array.from({ length: 5 }).map((_, i) => (
						<tr
							className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
							key={i}
						>
							{Array.from({ length: columns.length }).map((_, i) => (
								<td
									className="p-4 align-middle [&:has([role=checkbox])]:pr-0"
									key={i}
								>
									<Skeleton className="h-[20px] w-full" />
								</td>
							))}
						</tr>
					))}
				</tbody>
			</table>
		</div>
	);
};
