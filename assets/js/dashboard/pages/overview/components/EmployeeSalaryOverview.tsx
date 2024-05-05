import React from 'react';
import {
	Bar,
	BarChart,
	CartesianGrid,
	Legend,
	Rectangle,
	Tooltip,
	XAxis,
	YAxis,
} from 'recharts';

type Props = {
	data: Array<{
		name: string;
		employees: number;
	}>;
};

export const EmployeeSalaryOverview = (props: Props) => {
	return (
		<div className="[&>div]:!w-full">
			<BarChart
				width={500}
				height={300}
				data={props.data}
				margin={{
					top: 5,
					right: 30,
					left: 20,
					bottom: 5,
				}}
			>
				<CartesianGrid strokeDasharray="3 3" />
				<XAxis dataKey="name" />
				<YAxis />
				<Tooltip />
				<Legend />
				<Bar
					dataKey="employees"
					fill="#8884d8"
					activeBar={<Rectangle fill="pink" stroke="blue" />}
				/>
			</BarChart>
		</div>
	);
};
