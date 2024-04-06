import { z } from 'zod';

export const employeeSchema = z.object({
	name: z.string().min(1, 'Name is required').max(255),
	email: z.string().min(1, 'Email is required').email('Email is not valid'),
	phone_number: z
		.string()
		.min(1, 'Phone number is required')
		.regex(/^\+?[0-9]{1,3}-?[0-9]{3,}$/),
	address: z.string().min(1).max(255),
	date_of_birth: z.string().regex(/^\d{4}-\d{2}-\d{2}$/),
	date_of_employment: z.string().regex(/^\d{4}-\d{2}-\d{2}$/),
	salary: z.number().min(0),
	status: z.enum(['active', 'inactive', 'terminated']).default('inactive'),
	department: z.number().optional(),
	position: z.number().optional(),
});

export type EmployeeSchema = z.infer<typeof employeeSchema>;

export const departmentSchema = z.object({
	name: z.string().min(1, 'Name is required').max(255),
	description: z.string().optional(),
});

export type DepartmentSchema = z.infer<typeof departmentSchema>;

export const positionSchema = z.object({
	name: z.string().min(1, 'Name is required').max(255),
	description: z.string().optional(),
});

export type PositionSchema = z.infer<typeof positionSchema>;
