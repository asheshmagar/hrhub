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

export const DepartmentsOverview = (props: Props) => {
	console.log(props);

	return (
		<Bar
			options={{
				responsive: true,
				plugins: {
					legend: {
						position: 'top' as const,
					},
					title: {
						display: true,
						text: 'Departments',
					},
				},
				scales: {
					y: {
						suggestedMin: 0,
						suggestedMax: 10,
					},
				},
			}}
			data={{
				labels: props.data.map((item) => item.name),
				datasets: [
					{
						label: 'Employees',
						data: props.data.map((item) => item.employees),
						backgroundColor: 'black',
					},
				],
			}}
		/>
	);
};
