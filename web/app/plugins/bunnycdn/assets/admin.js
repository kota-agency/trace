/**
 * bunny.net WordPress Plugin
 * Copyright (C) 2024  BunnyWay d.o.o.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

let alertTimeout = null;

const formPreventUnload = (event) => {
    event.preventDefault();
    event.returnValue = false;
};

window.addEventListener('load', () => {
    // user info
    if (document.querySelector('header div.user-profile')) {
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'bunnycdn',
                section: 'user-data',
            },
            type: 'GET',
            complete: function (response) {
                const data = response?.responseJSON?.data;
                document.querySelector('header div.user-profile').classList.remove('loading');

                if (response?.responseJSON?.success === true) {
                    document.querySelector('header div.user-profile img').src = data.avatar_url;
                    document.querySelector('header div.user-profile [data-field=email]').innerText = data.email;
                    if (data.name.length > 0) {
                        document.querySelector('header div.user-profile [data-field=name]').innerText = '(' + data.name + ')';
                    } else {
                        document.querySelector('header div.user-profile [data-field=name]').innerText = '';
                    }
                } else {
                    document.querySelectorAll('header div.user-profile *').forEach((el) => {
                        el.classList.add('bn-d-none');
                    });

                    document.getElementById('user-profile-alert').classList.remove('bn-d-none');
                    document.getElementById('user-profile-alert').innerText = data?.message || 'User information not available.';
                }
            }
        });
    }

    // overview
    if (document.querySelector('main article.overview')) {
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'bunnycdn',
                section: 'overview',
                perform: 'get-api-data',
            },
            type: 'GET',
            complete: function (response) {
                document.querySelector('main article.overview').classList.remove('loading');

                if (response?.responseJSON?.success === true) {
                    const data = response.responseJSON.data;

                    document.querySelector('[data-api="overview-billing-balance"]').innerText = data.overview.billing.balance;
                    document.querySelector('[data-api="overview-month-charges"]').innerText = data.overview.month.charges;
                    document.querySelector('[data-api="overview-month-bandwidth"]').innerText = data.overview.month.bandwidth;
                    document.querySelector('[data-api="overview-month-bandwidth-avg-cost"]').innerText = data.overview.month.bandwidth_avg_cost;

                    updateOverviewBlock('bandwidth', data.bandwidth);
                    updateOverviewBlock('cache', data.cache);
                    updateOverviewBlock('requests', data.requests);

                    // charts
                    renderChart('bandwidth', data.chart.bandwidth, 'Data Used', 'bytes');
                    renderChart('cache', data.chart.cache, 'Cache Rate', 'percentage');
                    renderChart('requests', data.chart.requests, 'Requests', 'number');

                    return;
                }

                document.querySelector('article.overview div.container div.alert.red').classList.remove('bn-d-none');
                if (response?.responseJSON?.data?.message !== undefined) {
                    document.querySelector('article.overview div.container div.alert.red').innerText = response.responseJSON.data.message;
                }
            }
        });
    }

    // cdn
    document.getElementById('cdn-enabled')?.addEventListener('change', function () {
        const isEnabled = document.getElementById('cdn-enabled').checked;

        document.querySelectorAll('.hide-disabled').forEach((el) => {
            if (isEnabled) {
                el.classList.remove('bn-d-none');
            } else {
                el.classList.add('bn-d-none');
            }
        });
    });

    document.getElementById('cdn-acceleration-enable')?.addEventListener('click', function () {
        document.querySelector('#cdn-acceleration-enable-section div.alert')?.classList.add('bn-d-none');
        document.getElementById('cdn-acceleration-enable').classList.add('loading');
        const _wpnonce = document.getElementById('_wpnonce').value

        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'bunnycdn',
                section: 'cdn',
                perform: 'acceleration-enable',
                _wpnonce: _wpnonce,
            },
            type: 'POST',
            complete: function (data) {
                document.getElementById('cdn-acceleration-enable').classList.remove('loading');
                document.querySelector('#cdn-acceleration-enable-section div.alert')?.classList.remove('bn-d-none');
                document.querySelector('#cdn-acceleration-enable-section div.alert').innerText = data.responseJSON.data.message ?? 'Error';

                if (data.responseJSON.success) {
                    document.querySelector('#cdn-acceleration-enable-section div.alert').classList.remove('red');
                    document.querySelector('#cdn-acceleration-enable-section div.alert').classList.add('green');

                    window.removeEventListener('beforeunload', formPreventUnload);
                    location.reload();
                } else {
                    document.querySelector('#cdn-acceleration-enable-section div.alert').classList.remove('green');
                    document.querySelector('#cdn-acceleration-enable-section div.alert').classList.add('red');
                }
            },
        });
    });

    document.getElementById('cdn-acceleration-disable-section')?.closest('form').querySelector('#website-url').addEventListener('change', function () {
        const url = this.value;
        const _wpnonce = document.getElementById('_wpnonce').value
        document.getElementById('pullzone-id').classList.add('loading');

        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'bunnycdn',
                section: 'cdn',
                perform: 'get-pullzones',
                url: url,
                _wpnonce: _wpnonce,
            },
            type: 'GET',
            complete: function (response) {
                document.getElementById('pullzone-id').classList.remove('loading');

                if (response?.responseJSON?.success !== true) {
                    alert(data.responseJSON.data.message ?? 'Error loading pullzones.');
                    return;
                }

                const select = document.getElementById('pullzone-id');
                while (select.options.length > 1) {
                    const item = select.options[select.options.length - 1];
                    if (item.value !== '0') {
                        select.options.remove(select.options.length - 1);
                    }
                }

                const pullzones = response.responseJSON.data.pullzones || [];
                pullzones.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = `${item.name} (${item.id})`;
                    select.appendChild(option);
                });
            },
        });
    });

    document.getElementById('cdn-acceleration-disable')?.addEventListener('click', function () {
        document.querySelector('#cdn-acceleration-disable-section div.alert')?.classList.add('bn-d-none');
        document.getElementById('cdn-acceleration-disable').classList.add('loading');
        const _wpnonce = document.getElementById('_wpnonce').value

        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'bunnycdn',
                section: 'cdn',
                perform: 'acceleration-disable',
                url: document.getElementById('website-url').value,
                pullzone_id: document.getElementById('pullzone-id').value,
                _wpnonce: _wpnonce,
            },
            type: 'POST',
            complete: function (data) {
                document.getElementById('cdn-acceleration-disable').classList.remove('loading');
                document.querySelector('#cdn-acceleration-disable-section div.alert')?.classList.remove('bn-d-none');
                document.querySelector('#cdn-acceleration-disable-section div.alert').innerText = data.responseJSON.data.message ?? 'Error';

                if (data.responseJSON.success) {
                    document.querySelector('#cdn-acceleration-disable-section div.alert').classList.remove('red');
                    document.querySelector('#cdn-acceleration-disable-section div.alert').classList.add('green');

                    setTimeout(function () {
                        window.removeEventListener('beforeunload', formPreventUnload);
                        location.reload();
                    }, 2000);
                } else {
                    document.querySelector('#cdn-acceleration-disable-section div.alert').classList.remove('green');
                    document.querySelector('#cdn-acceleration-disable-section div.alert').classList.add('red');
                }
            },
        });
    });

    document.getElementById('cdn-cache-purge')?.addEventListener('click', function () {
        document.querySelector('#cdn-cache-purge-section div.alert')?.classList.add('bn-d-none');
        document.getElementById('cdn-cache-purge').classList.add('loading');
        const _wpnonce = document.getElementById('_wpnonce').value;

        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'bunnycdn',
                section: 'cdn-cache-purge',
                _wpnonce: _wpnonce,
            },
            type: 'POST',
            complete: function (data) {
                clearTimeout(alertTimeout);
                document.getElementById('cdn-cache-purge').classList.remove('loading');
                document.querySelector('#cdn-cache-purge-section div.alert')?.classList.remove('bn-d-none');

                if (data.status === 200 && data.responseJSON?.success === true) {
                    document.querySelector('#cdn-cache-purge-section div.alert').innerText = data.responseJSON?.data?.message ?? 'The cache was purged.';
                    document.querySelector('#cdn-cache-purge-section div.alert').classList.add('green');
                    document.querySelector('#cdn-cache-purge-section div.alert').classList.remove('red');
                } else {
                    document.querySelector('#cdn-cache-purge-section div.alert').innerText = data.responseJSON?.data?.message ?? 'Error';
                    document.querySelector('#cdn-cache-purge-section div.alert').classList.add('red');
                    document.querySelector('#cdn-cache-purge-section div.alert').classList.remove('green');
                }

                alertTimeout = setTimeout(() => {
                    document.querySelector('#cdn-cache-purge-section div.alert')?.classList.add('bn-d-none');
                    document.querySelector('#cdn-cache-purge-section div.alert').classList.remove('red');
                    document.querySelector('#cdn-cache-purge-section div.alert').classList.add('green');
                }, 3000);
            },
        });
    });

    // offloader
    if (document.querySelector('article.offloader section.statistics ul.statistics')) {
        updateOffloaderStatistics();
    }

    if (document.getElementById('offloader-sync-errors')) {
        updateOffloaderSyncErrors();

        document.getElementById('offloader-sync-errors').addEventListener('click', (event) => {
            if (event.target.tagName !== 'BUTTON') {
                return;
            }

            const id = event.target.attributes['data-attachment-id']?.value;
            const keep = event.target.attributes['data-keep']?.value;

            if (id === undefined || keep === undefined) {
                return;
            }

            const _wpnonce = document.getElementById('_wpnonce').value;
            const tr = document.querySelector('#offloader-sync-errors tr[data-attachment-id="' + id + '"]');

            tr.querySelector('div.actions').classList.add('loading');
            tr.querySelector('div.actions').innerHTML = '<span></span>';

            jQuery.ajax({
                url: ajaxurl,
                data: {
                    action: 'bunnycdn',
                    section: 'offloader',
                    perform: 'resolve-conflict',
                    attachment_id: id,
                    keep: keep,
                    _wpnonce: _wpnonce,
                },
                type: 'POST',
                complete: function (response) {
                    tr.querySelector('div.actions').classList.remove('loading');

                    if (response?.responseJSON?.success === true) {
                        tr.querySelector('div.actions').innerHTML = '<div class="alert compact green">Success</div>';
                        return;
                    }

                    const message = response?.responseJSON?.data?.message ?? 'An error occurred';
                    tr.querySelector('div.actions').innerHTML = '<div class="alert compact red">' + message + '</div>';
                }
            });
        });
    }

    document.getElementById('offloader-enabled')?.addEventListener('change', function () {
        const isEnabled = document.getElementById('offloader-enabled').checked;

        document.querySelectorAll('.hide-disabled').forEach((el) => {
            if (isEnabled) {
                el.classList.remove('bn-d-none');
            } else {
                el.classList.add('bn-d-none');
            }
        });

        document.querySelectorAll('.hide-enabled').forEach((el) => {
            if (isEnabled) {
                el.classList.add('bn-d-none');
            } else {
                el.classList.remove('bn-d-none');
            }
        });
    });

    document.querySelector('article.offloader form')?.addEventListener('submit', function (event) {
        const isEnabled = document.getElementById('offloader-enabled').checked;

        if (document.getElementById('offloader-enabled').checked != document.getElementById('offloader-enabled').defaultChecked) {
            event.preventDefault();

            if (isEnabled) {
                jQuery('#modal-offloader-enable').fadeIn();
            } else {
                jQuery('#modal-offloader-disable').fadeIn();
            }
        }
    });

    if (document.getElementById('offloader-replication')) {
        const updatePrice = () => {
            const count = 1 + document.getElementById('offloader-replication').querySelectorAll('input[type=checkbox]:checked:not(:disabled)').length;
            const priceCents = count * 2;
            document.getElementById('offloader-price').innerText = (new Intl.NumberFormat([], {minimumFractionDigits: 2})).format(priceCents / 100);
        };

        document.getElementById('offloader-replication').querySelectorAll('input[type=checkbox]').forEach((item) => item.addEventListener('change', () => updatePrice()));

        updatePrice();
    }

    bindModalConfirmEvents('modal-offloader-enable');
    bindModalConfirmEvents('modal-offloader-disable');

    // optimizer
    document.getElementById('optimizer-enabled')?.addEventListener('change', function () {
        const isEnabled = document.getElementById('optimizer-enabled').checked;

        if (isEnabled) {
            document.getElementById('optimizer-enabled').closest('section').querySelector('.alert').classList.add('bn-d-none');
        } else {
            document.getElementById('optimizer-enabled').closest('section').querySelector('.alert').classList.remove('bn-d-none');
        }

        document.querySelectorAll('.hide-disabled').forEach((el) => {
            if (isEnabled) {
                el.classList.remove('bn-d-none');
            } else {
                el.classList.add('bn-d-none');
            }
        });

        document.querySelectorAll('.hide-enabled').forEach((el) => {
            if (isEnabled) {
                el.classList.add('bn-d-none');
            } else {
                el.classList.remove('bn-d-none');
            }
        });
    });

    // reset
    bindModalConfirmEvents('modal-convert-agency-mode');
    bindModalConfirmEvents('modal-reset');

    document.getElementById('reset-btn')?.addEventListener('click', function () {
        jQuery('#modal-reset').fadeIn();
    });

    document.getElementById('reset-confirm')?.addEventListener('click', function () {
        document.getElementById('reset-btn').closest('form').submit();
    });

    document.getElementById('reset-cancel')?.addEventListener('click', function () {
        jQuery('#modal-reset').fadeOut();
    });

    document.getElementById('convert-agency-mode-btn')?.addEventListener('click', function () {
        jQuery('#modal-convert-agency-mode').fadeIn();
    });

    document.getElementById('convert-agency-mode-confirm')?.addEventListener('click', function () {
        document.getElementById('convert-agency-mode-btn').closest('form').submit();
    });

    document.getElementById('convert-agency-mode-cancel')?.addEventListener('click', function () {
        jQuery('#convert-agency-mode').fadeOut();
    });

    // warn user before leaving without saving the form
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('change', () => {
            window.addEventListener('beforeunload', formPreventUnload);

            document.querySelector('form').querySelectorAll('div.alert.green').forEach((item) => {
                item.classList.add('bn-d-none');
            });
        });

        form.addEventListener('submit', () => {
            window.removeEventListener('beforeunload', formPreventUnload);
        });
    });

    // input addon field-edit
    document.querySelectorAll('button[data-field-edit]').forEach((item) => {
        item.addEventListener('click', function (event) {
            event.preventDefault();
            const targetEl = item.attributes['data-field-edit']?.value;
            if (!targetEl) {
                return;
            }

            document.getElementById(targetEl).readOnly = false;
            document.getElementById(targetEl).focus();
            item.classList.add('bn-d-none');
        });
    });
});

function bindModalConfirmEvents(prefix) {
    document.getElementById(prefix + '-confirm')?.addEventListener('click', function (event) {
        event.preventDefault();

        if (document.getElementById(prefix + '-checkbox').checked) {
            document.getElementById(prefix + '-confirmed').closest('form').submit();
        } else {
            alert('You must acknowledge the changes.');
        }
    });

    document.getElementById(prefix + '-cancel')?.addEventListener('click', function (event) {
        event.preventDefault();
        document.getElementById(prefix + '-checkbox').checked = false;
        document.getElementById(prefix + '-confirm').disabled = true;
        document.getElementById(prefix + '-confirmed').value = '0';
        jQuery('#' + prefix).fadeOut();
    });

    document.getElementById(prefix + '-checkbox')?.addEventListener('change', function () {
        const checked = document.getElementById(prefix + '-checkbox').checked;
        document.getElementById(prefix + '-confirm').disabled = !checked;
        document.getElementById(prefix + '-confirmed').value = (checked ? '1' : '0');
    });
}

function updateOverviewBlock(el, data) {
    const directionContext = {
        'down': 'danger',
        'equal': 'warning',
        'up': 'success'
    };

    document.querySelector('[data-api="' + el + '-total"]').innerText = data.total;
    document.querySelector('[data-api="' + el + '-trend"]').classList.add(`bn-badge--${directionContext[data.trend.direction]}`);
    document.querySelector('[data-api="' + el + '-trend"] .bn-badge__text').innerText = data.trend.value;
    document.querySelector('[data-api="' + el + '-trend"] .bn-badge__icon').classList.add(`bn-badge__icon--${data.trend.direction}`);
}

function updateOffloaderStatistics()
{
    const container = document.querySelector('article.offloader section.statistics ul.statistics');
    if (!container) {
        return;
    }

    container.classList.add('loading');

    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'bunnycdn',
            section: 'offloader',
            perform: 'get-statistics',
        },
        type: 'GET',
        complete: function (response) {
            container.classList.remove('loading');
            const data = response?.responseJSON?.data;
            Object.keys(data).forEach((key) => {
                container.querySelector('[data-label="' + key + '"] span.count').innerText = data[key];
            });
        }
    });
}

function updateOffloaderSyncErrors()
{
    const container = document.getElementById('offloader-sync-errors');
    if (!container) {
        return;
    }

    container.classList.add('loading');

    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'bunnycdn',
            section: 'offloader',
            perform: 'get-sync-errors',
        },
        type: 'GET',
        complete: function (response) {
            container.classList.remove('loading');
            const data = response?.responseJSON?.data;
            container.querySelector('tbody').innerHTML = '';
            const trTemplate = document.querySelector('#offloader-sync-errors template.tbody').innerHTML;

            Object.keys(data).forEach((key) => {
                let html = trTemplate;
                html = html.replaceAll('{{id}}', data[key].id);
                html = html.replaceAll('{{reason}}', data[key].reason);
                html = html.replaceAll('{{filename}}', 'wp-content/uploads/' + data[key].path);
                container.querySelector('tbody').innerHTML = container.querySelector('tbody').innerHTML + html;
            });
        }
    });
}

function dateFormatter(value) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    return (new Date(value)).getDate() + ' ' + months[(new Date(value)).getMonth()];
}

const bytesUnits = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];

function bytesFormatter(precision, index = 0) {
    return function (value) {
        for (let i = 0; i < index; i++) {
            value = value / 1024;
        }

        return value.toFixed(precision) + ' ' + bytesUnits[index];
    };
}

function getUnitIndex(data) {
    let index = 0;
    let max = data.reduce((prev, cur) => {
        if (cur[1] > prev) {
            return cur[1];
        } else {
            return prev;
        }
    }, 0);

    while (max > 1024) {
        max = max / 1024;
        index++;
    }

    if (index > 0 && max < 10) {
        index = index - 1;
    }

    return index;
}

function renderChart(elId, data, xLabel, dataFormat) {
    let dataFormatter = (value) => value.toFixed(0);
    let tooltipFormatter = function (data) {
        return dateFormatter(data[0].data[0]) + ': <b>' + dataFormatter(data[0].data[1]) + '</b>';
    };

    switch (dataFormat) {
        case 'bytes':
            const unitIndex = getUnitIndex(data);
            dataFormatter = bytesFormatter(0, unitIndex);
            tooltipFormatter = function (data) {
                return dateFormatter(data[0].data[0]) + ': <b>' + bytesFormatter(2, unitIndex)(data[0].data[1]) + '</b>';
            };
            break;

        case 'percentage':
            dataFormatter = (value) => value.toFixed(2) + '%';
            tooltipFormatter = function (data) {
                return dateFormatter(data[0].data[0]) + ': <b>' + dataFormatter(data[0].data[1]) + '</b>';
            };
            break;
    }

    const chart = echarts.init(document.querySelector(`div[data-chart="${elId}"]`));
    chart.setOption({
        xAxis: {
            type: 'time',
            axisLabel: {
                formatter: dateFormatter,
                color: '#687a8b'
            },
            axisLine: {
                lineStyle: {
                    color: '#9BA7B2',
                },
            },
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: dataFormatter,
                color: '#687a8b'
            },
        },
        tooltip: {
            trigger: 'axis',
            formatter: tooltipFormatter,
            axisPointer: {
                type: 'none'
            },
        },
        series: [
            {
                name: xLabel,
                data: data,
                smooth: true,
                showSymbol: false,
                type: 'line',
            },
        ],
    });

    window.addEventListener('resize', () => {
        chart.resize();
    });
}
