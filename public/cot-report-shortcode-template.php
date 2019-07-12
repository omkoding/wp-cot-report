<div id="cot-page" v-cloak>
    <h2>{{ symbols[symbol] }} &mdash; {{ year }}</h2>

    <p>
    	Year:
    	<a v-for="item in years"
    		class="cot-year"
    		:href="pageUrl + '?cot-symbol=' + symbol + '&cot-year=' + item"
    	>
    		<strong v-if="item == year">{{ item }}</strong>
    		<span v-else>{{ item }}</span>
    	</a>
    </p>

    <cot-table :rows="reports" inline-template>
        <table class="wp-block-table is-style-stripes cot-table">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th class="has-text-centered" colspan="7">Non-Commercials</th>
                    <th class="has-text-centered" colspan="4">Open Interest (OI)</th>
                </tr>
                <tr>
                    <th class="has-text-centered">Date</th>
                    <th class="has-text-centered">Long</th>
                    <th class="has-text-centered">Long +/-</th>
                    <th class="has-text-centered">Short</th>
                    <th class="has-text-centered">Short +/-</th>
                    <th class="has-text-centered">Long %</th>
                    <th class="has-text-centered">Short %</th>
                    <th class="has-text-centered">Net</th>
                    <th class="has-text-centered">Total</th>
                    <th class="has-text-centered">+/-</th>
                    <th class="has-text-centered">Long %</th>
                    <th class="has-text-centered">Short %</th>
                </tr>
            </thead>
            <tbody is="cot-table-row" v-for="item in rows" :report="item" :key="item.date" inline-template>
                <tr>
                    <td class="has-text-centered">{{ report.date }}</td>
                    <td class="has-text-right">{{ report.current_nc_long.toLocaleString() }}</td>
                    <td class="has-text-right"
                        :class="{'has-text-danger': report.changes_nc_long < 0}"
                    >{{ report.changes_nc_long.toLocaleString() }}</td>
                    <td class="has-text-right">{{ report.current_nc_short.toLocaleString() }}</td>
                    <td class="has-text-right"
                        :class="{'has-text-danger': report.changes_nc_short < 0}"
                    >{{ report.changes_nc_short.toLocaleString() }}</td>
                    <td class="has-text-right">{{ longPercentage }}%</td>
                    <td class="has-text-right">{{ shortPercentage }}%</td>
                    <td class="has-text-right"
                        :class="{'has-text-danger': nonCommercialNet < 0}"
                    >{{ nonCommercialNet.toLocaleString() }}</td>
                    <td class="has-text-right">{{ report.oi_current.toLocaleString() }}</td>
                    <td class="has-text-right"
                        :class="{'has-text-danger': report.oi_change < 0}"
                    >{{ report.oi_change.toLocaleString() }}</td>
                    <td class="has-text-right">{{ report.oi_nc_long }}</td>
                    <td class="has-text-right">{{ report.oi_nc_short }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="has-text-right"><strong>{{ totalLong.toLocaleString() }}</strong></td>
                    <td></td>
                    <td class="has-text-right"><strong>{{ totalShort.toLocaleString() }}</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </cot-table>

    <hr>

    <h2>Available Symbols</h2>

    <template v-for="(item, index) in symbols">
	    <h3>
	    	<a :href="pageUrl + '?cot-symbol=' + index" :title="'COT Report' + item">COT Report {{ item }}</a>
	    </h3>
	</template>
</div>