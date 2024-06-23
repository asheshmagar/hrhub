import {
	BarElement,
	CategoryScale,
	Chart as ChartJS,
	Legend,
	LinearScale,
	Title,
	Tooltip,
} from 'chart.js';
import React from 'react';
import { Bar } from 'react-chartjs-2';

type Props = {
	data: Array<{
		name: string;
		employees: number;
	}>;
};

ChartJS.register(
	CategoryScale,
	LinearScale,
	BarElement,
	Title,
	Tooltip,
	Legend,
);

export const EmployeeSalaryOverview = (props: Props) => {
	const labels = props.data.map((item) => item.name);
	const data = {
		labels,
		datasets: [
			{
				label: 'Employees',
				data: props.data.map((item) => item.employees),
				backgroundColor: 'rgba(255, 99, 132, 0.5)',
			},
		],
	};

	return (
		<div className="[&>div]:!w-full">
			<Bar
				options={{
					responsive: true,
					plugins: {
						legend: {
							position: 'top' as const,
						},
						title: {
							display: true,
							text: 'Chart.js Bar Chart',
						},
					},
				}}
				data={data}
			/>
		</div>
	);
};
