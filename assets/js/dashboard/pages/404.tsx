import React from 'react';

export const NotFound = () => {
	return (
		<div className="h-full w-full flex flex-col justify-center items-center mt-20">
			<h1 className="text-9xl font-extrabold text-black tracking-widest">
				404
			</h1>
			<div className="bg-gray-300 px-2 text-sm rounded rotate-12 absolute">
				Page Not Found
			</div>
		</div>
	);
};
