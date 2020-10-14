/**
 * External dependencies
 */
import PropTypes from 'prop-types'
import classnames from 'classnames'

/**
 * WordPress dependencies
 */
import { Icon, more } from '@wordpress/icons'
import { Component } from '@wordpress/element'
import { Button, Dropdown, NavigableMenu } from '@wordpress/components'

/**
 * This is a dropdown menu hidden behind a vertical ellipsis icon. When clicked, the inner MenuItems are displayed.
 */
class EllipsisMenu extends Component {
	render() {
		const { label, renderContent } = this.props
		if ( ! renderContent ) {
			return null
		}

		const renderEllipsis = ( { onToggle, isOpen } ) => {
			const toggleClassname = classnames(
				'rank-math-ellipsis-menu__toggle',
				'button',
				{
					'is-opened': isOpen,
				}
			)

			return (
				<Button
					className={ toggleClassname }
					onClick={ onToggle }
					icon="ellipsis"
					title={ label }
					aria-expanded={ isOpen }
				>
					<Icon icon={ more } />
				</Button>
			)
		}

		const renderMenu = ( renderContentArgs ) => (
			<NavigableMenu className="rank-math-ellipsis-menu__content">
				{ renderContent( renderContentArgs ) }
			</NavigableMenu>
		)

		return (
			<div className="rank-math-ellipsis-menu">
				<Dropdown
					contentClassName="rank-math-ellipsis-menu__popover"
					position="bottom left"
					renderToggle={ renderEllipsis }
					renderContent={ renderMenu }
				/>
			</div>
		)
	}
}

EllipsisMenu.propTypes = {
	/**
	 * The label shown when hovering/focusing on the icon button.
	 */
	label: PropTypes.string.isRequired,
	/**
	 * A function returning `MenuTitle`/`MenuItem` components as a render prop. Arguments from Dropdown passed as function arguments.
	 */
	renderContent: PropTypes.func,
}

export default EllipsisMenu
