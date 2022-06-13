/**
 * External dependencies
 */
import { map } from 'lodash'
import classnames from 'classnames'
import { HashRouter as Router, NavLink, Route, Switch } from 'react-router-dom'

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n'
import { applyFilters } from '@wordpress/hooks'
import { Fragment } from '@wordpress/element'

/**
 * Internal dependencies
 */
import Analytics from './Analytics/Analytics'
import Dashboard from './Dashboard/Dashboard'
import Performance from './Performance/Performance'
import URLInspection from './URLInspection/URLInspection'
import Keywords from './Keywords/Keywords'
import Single from './Single/Single'
import KeywordsTracked from './Keywords/KeywordsTracked'

const getTabs = () => {
	const tabs = []

	tabs.push( {
		path: '/',
		title: (
			<Fragment>
				<i
					className="rm-icon rm-icon-acf"
					title={ __( 'Dashboard', 'rank-math' ) }
				></i>
				<span>{ __( 'Dashboard', 'rank-math' ) }</span>
			</Fragment>
		),
		view: Dashboard,
		className: 'rank-math-dashboard-tab',
	} )

	tabs.push( {
		path: '/analytics/:paged',
		link: '/analytics/1',
		exact: false,
		title: (
			<Fragment>
				<i
					className="rm-icon rm-icon-analytics"
					title={ __( 'Site Analytics', 'rank-math' ) }
				></i>
				<span>{ __( 'Site Analytics', 'rank-math' ) }</span>
			</Fragment>
		),
		view: Analytics,
		className: 'rank-math-analytics-tab',
	} )

	tabs.push( {
		path: '/performance/:paged',
		link: '/performance/1',
		exact: false,
		title: (
			<Fragment>
				<i
					className="rm-icon rm-icon-analyzer"
					title={ __( 'SEO Performance', 'rank-math' ) }
				></i>
				<span>{ __( 'SEO Performance', 'rank-math' ) }</span>
			</Fragment>
		),
		view: Performance,
		className: 'rank-math-performance-tab',
	} )

	tabs.push( {
		path: '/keywords/:paged',
		link: '/keywords/1',
		exact: false,
		title: (
			<Fragment>
				<i
					className="rm-icon rm-icon-faq"
					title={ __( 'Keywords', 'rank-math' ) }
				></i>
				<span>{ __( 'Keywords', 'rank-math' ) }</span>
			</Fragment>
		),
		view: Keywords,
		className: 'rank-math-keywords-tab',
	} )

	tabs.push( {
		path: '/single/:id',
		view: Single,
		className: 'rank-math-single-tab',
	} )

	tabs.push( {
		path: '/tracker/:paged',
		link: '/tracker/1',
		exact: false,
		title: (
			<Fragment>
				<i
					className="rm-icon rm-icon-acf"
					title={ __( 'Rank Tracker', 'rank-math' ) }
				></i>
				<span>{ __( 'Rank Tracker', 'rank-math' ) }</span>
			</Fragment>
		),
		view: KeywordsTracked,
		className: 'rank-math-tracker-tab',
	} )

	if ( rankMath.enableIndexStatus ) {
		tabs.push( {
			path: '/indexing/:paged',
			link: '/indexing/1',
			exact: false,
			title: (
				<Fragment>
					<i
						className="rm-icon rm-icon-analyzer"
						title={ __( 'Index Status', 'rank-math' ) }
					></i>
					<span>{ __( 'Index Status', 'rank-math' ) }</span>
				</Fragment>
			),
			view: URLInspection,
			className: 'rank-math-indexing-tab',
			isNew: ! rankMath.viewedIndexStatus,
		} )
	}

	return applyFilters( 'rank_math_search_console_tabs', tabs )
}

const App = () => {
	const tabs = getTabs()

	return (
		<Router>
			<div className="rank-math-tabs horizontal">
				<div
					className="rank-math-tab-nav"
					role="tablist"
					aria-orientation="horizontal"
				>
					{ map(
						tabs,
						( {
							path,
							title = false,
							exact = true,
							link = false,
							isNew = false,
						} ) => {
							if ( false === title ) {
								return null
							}

							return (
								<NavLink
									exact={ exact }
									className={ isNew ? 'rank-math-tab is-new' : 'rank-math-tab' }
									activeClassName="is-active"
									key={ path }
									to={ link ? link : path }
									isActive={ ( match, loc ) => {
										const check = link === false ? path : link
										return check.replace( /\/[0-9]+$/g, '' ) === loc.pathname.replace( /\/[0-9]+$/g, '' )
									} }
								>
									{ title }
								</NavLink>
							)
						}
					) }

					{ '' !== rankMath.lastUpdated && (
						<div className="rank-math-updated"><strong>{ __( 'Last updated on', 'rank-math' ) }</strong><br /> { rankMath.lastUpdated }</div>
					) }
				</div>
				<Switch>
					{ map(
						tabs,
						( {
							path,
							view: Component,
							exact = true,
							className,
						} ) => {
							return (
								<Route
									exact={ exact }
									key={ path }
									path={ path }
									render={ ( props ) => {
										const wrapper = classnames(
											'rank-math-tab-content',
											className
										)
										return (
											<div className={ wrapper }>
												<Component { ...props } />
												{ '/indexing/:paged' !== path && <p className="rank-math-footnote"><strong>{ __( 'Note:', 'rank-math' ) }</strong> { __( 'The statistics that appear in the Rank Math Analytics module won’t match with the data from the Google Search Console as we only track posts and keywords that rank in the top 100 positions in the selected timeframe. We do this to help make decision-making easier and for faster data processing since this is the data you really need to prioritize your SEO efforts on.', 'rank-math' ) }</p> }
											</div>
										)
									} }
								/>
							)
						}
					) }
				</Switch>
			</div>
		</Router>
	)
}

export default App
