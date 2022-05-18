/**
 * External dependencies
 */
import PropTypes from 'prop-types'

/**
 * A component to display summarized table data - the list of data passed in on a single line.
 *
 * @param {Object} props
 * @param {Array} props.data
 * @return {Object} -
 */
const TableSummary = ( { data } ) => {
	return (
		<ul className="rank-math-table__summary">
			{ data.map( ( { label, value }, i ) => (
				<li className="rank-math-table__summary-item" key={ i }>
					<span className="rank-math-table__summary-value">
						{ value }
					</span>
					<span className="rank-math-table__summary-label">
						{ label }
					</span>
				</li>
			) ) }
		</ul>
	)
}

TableSummary.propTypes = {
	/**
	 * An array of objects with `label` & `value` properties, which display on a single line.
	 */
	data: PropTypes.array,
}

export default TableSummary
