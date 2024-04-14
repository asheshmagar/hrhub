import { z } from 'zod';

const MAX_FILE_SIZE = 1024 * 1024 * 2;

const ACCEPTED_FILE_TYPES = ['jpeg', 'jpg', 'png', 'webp', 'pdf'];

export const attachmentSchema = z.object({
	id: z.number(),
	title: z.string(),
	filename: z.string(),
	url: z.string().url(),
	link: z.string().url(),
	alt: z.string().optional(),
	author: z.string(), // Assuming author is always a string
	description: z.string().optional(),
	caption: z.string().optional(),
	name: z.string(),
	status: z.string(),
	uploadedTo: z.number(),
	date: z.string().regex(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z$/),
	modified: z.string().regex(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z$/),
	menuOrder: z.number(),
	mime: z.string(),
	type: z.string(),
	subtype: z.string(),
	icon: z.string().url(),
	dateFormatted: z.string(),
	nonces: z.object({
		update: z.string(),
		delete: z.string(),
		edit: z.string(),
	}),
	editLink: z.string().url(),
	meta: z.boolean(),
	authorName: z.string(),
	authorLink: z.string().url(),
	filesizeInBytes: z.number(),
	filesizeHumanReadable: z.string(),
	context: z.string().optional(),
	height: z.number(),
	width: z.number(),
	orientation: z.string(),
	sizes: z.object({
		thumbnail: z.object({
			height: z.number(),
			width: z.number(),
			url: z.string().url(),
			orientation: z.string(),
		}),
		medium: z.object({
			height: z.number(),
			width: z.number(),
			url: z.string().url(),
			orientation: z.string(),
		}),
		large: z.object({
			height: z.number(),
			width: z.number(),
			url: z.string().url(),
			orientation: z.string(),
		}),
		full: z.object({
			url: z.string().url(),
			height: z.number(),
			width: z.number(),
			orientation: z.string(),
		}),
	}),
	compat: z.object({
		item: z.string(),
		meta: z.string(),
	}),
});

export type AttachmentSchema = z.infer<typeof attachmentSchema>;

export const employeeSchema = z.object({
	id: z.number().optional(),
	name: z.string().min(1, 'Name is required').max(255),
	email: z.string().min(1, 'Email is required').email('Email is not valid'),
	phone_number: z
		.string()
		.min(1, 'Phone number is required')
		.regex(/^\+?[0-9]{1,3}-?[0-9]{3,}$/),
	address: z.string().min(1).max(255),
	date_of_birth: z.string().regex(/^\d{4}-\d{2}-\d{2}$/),
	date_of_employment: z.string().regex(/^\d{4}-\d{2}-\d{2}$/),
	salary: z.number().min(0).optional(),
	status: z.enum(['active', 'inactive', 'terminated']).default('inactive'),
	department: z.number().optional(),
	position: z.number().optional(),
	employment_type: z
		.enum(['full-time', 'part-time', 'trainee/intern', 'contractor/freelancer'])
		.default('full-time'),
	documents: z.array(attachmentSchema).optional(),
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
