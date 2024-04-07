import React from 'react';
import { DropzoneOptions, DropzoneState, useDropzone } from 'react-dropzone';

type Props = {
	children?: (
		dropzoneProps: Omit<DropzoneState, 'getRootProps' | 'getInputProps'>,
	) => React.ReactNode;
	className?: string;
	disclaimer?: string | React.ReactNode;
} & DropzoneOptions;

export const Dropzone = ({
	children,
	className,
	disclaimer,
	...props
}: Props) => {
	const { getRootProps, getInputProps, ...dropzoneProps } = useDropzone({
		...props,
	});
	return (
		<div>
			<div
				{...getRootProps({
					className: className,
				})}
			>
				<>
					<input {...getInputProps()} />
					{disclaimer}
				</>
			</div>
			{children?.(dropzoneProps)}
		</div>
	);
};

export default Dropzone;
