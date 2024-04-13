import React, { useState } from 'react';
import { Document, Page } from 'react-pdf';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from './ui/dialog';

type Props = {
	preview: {
		type: string;
		url: string;
	} | null;
	onClose: () => void;
};

export const DocumentPreview = ({ onClose, preview }: Props) => {
	const [numPages, setNumPages] = useState<number>();
	const [pageNumber, setPageNumber] = useState<number>(1);
	if (!preview) return null;
	return (
		<Dialog open={!!preview} onOpenChange={onClose}>
			<DialogContent className="max-h-full">
				<DialogHeader>
					<DialogTitle>Preview</DialogTitle>
				</DialogHeader>
				{preview.type === 'image' ? (
					<img className="object-contain mx-auto" src={preview.url} />
				) : (
					<Document
						file={preview.url}
						onLoadSuccess={({ numPages }) => {
							setNumPages(numPages);
						}}
					>
						<Page
							pageNumber={pageNumber}
							renderAnnotationLayer={false}
							renderTextLayer={false}
						/>
					</Document>
				)}
			</DialogContent>
		</Dialog>
	);
};
