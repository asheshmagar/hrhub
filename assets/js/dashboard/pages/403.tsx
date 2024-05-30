import { __ } from '@wordpress/i18n';
import React from 'react';
export const NoPermission = () => {
	return (
		<div className="h-full w-full flex flex-col justify-center items-center mt-20">
			<h1 className="text-9xl font-extrabold text-black tracking-widest">
				{__('403', 'hrhub')}
			</h1>
			<div className="bg-gray-300 px-2 text-sm rounded rotate-12 absolute uppercase">
				{__('Not this time, access forbidden', 'hrhub')}
			</div>
		</div>
	);
};
