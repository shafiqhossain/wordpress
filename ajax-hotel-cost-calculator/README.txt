INTRODUCTION:
This plugin provide a short code, which actually display some calculation based on days and average cost. You can simply put this 
calculator anywhere in the page by placing "[hotel-cost-calculator ]". You can customiza and style it at lot. 
Please see below for detail.


INSTALLATION:
Extract and put the folder into WordPress plugin directory and enable. Done!

SCENARIO:
1. Basic
Place this shot code anywhere in the page [hotel-cost-calculator ], it will calculate the WeShare savings assuming 18% by default.

2. Multiple calculator in same page
If you want to place multiple calculator in same page, you need to provide "cindex" attribute with the short code. 
Example: [hotel-cost-calculator cindex="3"]. Without "cindex", all calculator will show same results.

ATTRIBUTES:
1. "title"			: Optionally if "title" is provided, it will show at the top of each calculator. No title, nothing will display.
2. "pfactor"		: Optionally you can provide diffierent premium factor to different calculator. If not provided, it will take 0.18 by default.
3. "cindex"			: This attribute is required and need to be unique between multiple calculator shown in same page. By default, if not provided, it will take assign "1".
4. "currency"		: Optionally you can provide currency symbol. by default it will show "$".
5. "outer_border"	: Optionally you can display outer border line for the calculator. By default, it will not show any outer border. "0" means, don't show any outer border, "1" means, show the outer border.
6. "outer_divider"	: Optionally will show an outer divider.  By default, it will not show any outer divider. "0" means, don't show any divider, "1" means, show the outer divider.
7. "inner_border"	: Optionally you can display inner border line for the calculator. By default, it will not show any inner border. "0" means, don't show any inner border, "1" means, show the inner border.
8. "inner_divider"	: Optionally will show an inner divider.  By default, it will not show any inner divider. "0" means, don't show any inner divider, "1" means, show the inner divider.
9. "top_desc"		: Optionally will show the description text at the top and below title.
10. "bottom_desc"	: Optionally will show the description text at the bottom and below the calculator rows.

EXAMPLES:
[hotel-cost-calculator ]
[hotel-cost-calculator title="Calculate Your Cost in US" ]
[hotel-cost-calculator title="Calculate Your Cost in US" cindex="3"]
[hotel-cost-calculator title="Calculate Your Cost in Canada" pfactor="0.24" cindex="1" currency="$" outer_border = "1" outer_divider = "1" inner_border = "0" inner_divider = "0" top_desc="Calculate your savings quickly and get weshare savings"]
[hotel-cost-calculator title="Calculate Your Cost in Maxico" pfactor="0.18" cindex="2" currency="EUR" inner_border = "1" inner_divider = "1" bottom_desc="Calculate your savings quickly and get weshare savings"]

SUPPORT:
If you have any question or facing any issues, please contact:

Shafiq Hossain
shafiqhossain@yahoo.com
https://github.com/shafiqhossain
