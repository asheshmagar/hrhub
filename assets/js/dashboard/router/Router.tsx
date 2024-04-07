import React from 'react';
import { Route, Routes } from 'react-router-dom';
import { Departments } from '../pages/departments/Departments';
import { EditDepartment } from '../pages/departments/EditDepartment';
import { NewDepartment } from '../pages/departments/NewDepartment';
import { EditEmployee } from '../pages/employees/EditEmployee';
import { Employees } from '../pages/employees/Employees';
import { NewEmployee } from '../pages/employees/NewEmployee';
import { Overview } from '../pages/overview/Overview';
import { EditPosition } from '../pages/positions/EditDepartment';
import { NewPosition } from '../pages/positions/NewPosition';
import { Positions } from '../pages/positions/Positions';

export const Router = () => {
	return (
		<Routes>
			<Route path="/" element={<Overview />} />
			<Route path="/employees" element={<Employees />} />
			<Route path="/employees/new" element={<NewEmployee />} />
			<Route path="/employees/:employeeId/edit" element={<EditEmployee />} />
			<Route path="/departments" element={<Departments />} />
			<Route path="/departments/new" element={<NewDepartment />} />
			<Route
				path="/departments/:departmentId/edit"
				element={<EditDepartment />}
			/>
			<Route path="/positions" element={<Positions />} />
			<Route path="/positions/new" element={<NewPosition />} />
			<Route path="/positions/:positionId/edit" element={<EditPosition />} />
		</Routes>
	);
};
