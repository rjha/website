# web folder - use an ignore list
# web folder - needs custom definitions

./cloc-1.56.pl  --exclude-list-file=./cloc-ignore --read-lang-def=./cloc.def   /home/rjha/code/github/website/web --report-file=cloc.web.report
./cloc-1.56.pl /home/rjha/code/github/website/lib --report-file=cloc.lib.report
# sum the reports 
./cloc-1.56.pl  --read-lang-def=./cloc.def  --sum-reports *.report
#remove tmp
rm *.report


