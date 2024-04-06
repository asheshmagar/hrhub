import { ChevronDownIcon, X } from 'lucide-react';
import React from 'react';
import Select, {
	ClearIndicatorProps,
	components,
	DropdownIndicatorProps,
	MultiValueRemoveProps,
	Props as ReactSelectProps,
} from 'react-select';
import makeAnimated from 'react-select/animated';
import CreatableSelect from 'react-select/creatable';
import { cn } from '../../lib/utils';

const DropdownIndicator = (props: DropdownIndicatorProps) => {
	return (
		<components.DropdownIndicator {...props}>
			<ChevronDownIcon className="h-4 w-4 opacity-50" />
		</components.DropdownIndicator>
	);
};

const ClearIndicator = (props: ClearIndicatorProps) => {
	return (
		<components.ClearIndicator {...props}>
			<X className="h-4 w-4 opacity-50" />
		</components.ClearIndicator>
	);
};

const MultiValueRemove = (props: MultiValueRemoveProps) => {
	return (
		<components.MultiValueRemove {...props}>
			<X />
		</components.MultiValueRemove>
	);
};

const controlStyles = {
	base: 'border border-border rounded-md bg-background hover:cursor-pointer',
	focus: 'border-border ring-ring ring-primary-500',
	nonFocus: 'border-border',
};

const placeholderStyles = 'text-sm';
const selectInputStyles = 'text-foreground text-sm';
const valueContainerStyles = 'text-foreground text-sm px-3';
const singleValueStyles = 'ml-1';
const multiValueStyles =
	'ml-1 bg-background border border-border rounded items-center py-0.5 pl-2 pr-1 gap-1.5';
const multiValueLabelStyles = 'leading-6 py-0.5';
const multiValueRemoveStyles =
	'border border-gray-200 bg-white hover:bg-red-50 hover:text-red-800 text-gray-500 hover:border-red-300 rounded-md bg-background';
const indicatorsContainerStyles = 'p-1 gap-1 bg-background rounded-lg';
const clearIndicatorStyles = 'p-1';
const indicatorSeparatorStyles = 'bg-mutated';
const dropdownIndicatorStyles = 'p-1';
const menuStyles =
	'mt-2 p-2 border border-border bg-background text-sm rounded-lg';
const optionsStyle =
	'bg-background p-2 border-0 text-base hover:bg-secondary hover:cursor-pointer';
const groupHeadingStyles = 'ml-3 mt-2 mb-1 text-gray-500 text-sm bg-background';
const noOptionsMessageStyles = 'text-muted-foreground bg-background';

type SelectComponentProps = {
	options: any[];
	value?: any;
	onChange?: (value: any) => void;
	isMulti?: boolean;
	isDisabled?: boolean;
	isLoading?: boolean;
	createAble: boolean;
	placeholder?: string;
} & ReactSelectProps;

const ReactSelect = ({
	options,
	value,
	onChange,
	isMulti,
	isDisabled,
	isLoading,
	createAble,
	placeholder,
	...props
}: SelectComponentProps) => {
	const animatedComponents = makeAnimated();
	const Comp = createAble ? CreatableSelect : Select;
	return (
		<>
			<Comp
				unstyled
				isClearable
				isSearchable
				value={value}
				isDisabled={isDisabled}
				isMulti={isMulti}
				isLoading={isLoading}
				placeholder={placeholder}
				components={{
					...animatedComponents,
					DropdownIndicator: DropdownIndicator,
					ClearIndicator: ClearIndicator,
				}}
				// defaultInputValue={defaultValue}
				defaultValue={value}
				options={options}
				noOptionsMessage={() => 'No options found !!'}
				onChange={onChange}
				classNames={{
					control: ({ isFocused }) =>
						cn(
							isFocused ? controlStyles.focus : controlStyles.nonFocus,
							controlStyles.base,
						),
					placeholder: () => placeholderStyles,
					input: () => selectInputStyles,
					option: () => optionsStyle,
					menu: () => menuStyles,
					valueContainer: () => valueContainerStyles,
					singleValue: () => singleValueStyles,
					multiValue: () => multiValueStyles,
					multiValueLabel: () => multiValueLabelStyles,
					multiValueRemove: () => multiValueRemoveStyles,
					indicatorsContainer: () => indicatorsContainerStyles,
					clearIndicator: () => clearIndicatorStyles,
					indicatorSeparator: () => indicatorSeparatorStyles,
					dropdownIndicator: () => dropdownIndicatorStyles,
					groupHeading: () => groupHeadingStyles,
					noOptionsMessage: () => noOptionsMessageStyles,
				}}
				{...props}
			/>
		</>
	);
};

export { ReactSelect, DropdownIndicator, ClearIndicator, MultiValueRemove };
