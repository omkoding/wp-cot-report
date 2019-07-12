Vue.component('cot-table', {
	props: {
    rows: {
      type: Array,
      required: true,
    }
  },
  computed: {
    totalLong: function () {
      return this.rows.reduce((prev,next) => prev + next.changes_nc_long,0);
    },
    totalShort: function () {
      return this.rows.reduce((prev,next) => prev + next.changes_nc_short,0);
    }
  }
})

Vue.component('cot-table-row', {
	props: {
		report: {
			type: Object,
      required: true,
		}
	},
  computed: {
    longPercentage: function () {
      return (
        this.report.current_nc_long / 
        (
          this.report.current_nc_long + this.report.current_nc_short
        ) * 100
      ).toFixed(1);
    },
    shortPercentage: function () {
      return (100 - this.longPercentage).toFixed(1);
    },
    nonCommercialNet: function () {
      return this.report.current_nc_long - this.report.current_nc_short;
    }
  }
})

var app = new Vue({
  el: '#cot-page',
  data: {
    pageUrl: cot.page_url,
  	symbols: cot.symbols,
  	reports: cot.reports,
    symbol: cot.symbol,
    year: cot.year,
  },
  computed: {
    years: function () {
      var years = [];

      var currentYear = new Date().getFullYear();

      startYear = 2005;

      while ( startYear <= currentYear ) {
        years.push(startYear++);
      }

      return years;
    }
  }
})
