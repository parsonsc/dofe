jQuery.noConflict();

(function($){
    var patterns = {
        letters: /^[\sa-z'-]+$/i,
        ukpostcode: /^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9]?[A-Za-z])))) {0,1}[0-9][A-Za-z]{2})$/,
        telephone: /^([\+(][+0-9()]{1,5}([ )\-])?)?([\(]{1}[0-9]{3}[\)])?([0-9 \-]{1,256}([ \s\-])?)((x|ext|extension)?[0-9]{1,4}?)$/i,
        mobile: /^((0|\+44|00\d{2})7(5|6|7|8|9){1}\d{2}\s?\d{6})$/,
        decimal: /^\d*(\.[0-9]{1,2})?$/i,
        mastercard: /^5[1-5]\d{14}$/,
        visa: /(^4\d{12}$)|(^4[0-8]\d{14}$)|(^(49)[^013]\d{13}$)|(^(49030)[0-1]\d{10}$)|(^(49033)[0-4]\d{10}$)|(^(49110)[^12]\d{10}$)|(^(49117)[0-3]\d{10}$)|(^(49118)[^0-2]\d{10}$)|(^(493)[^6]\d{12}$)/,
        maestro: /(^(5[0678])\d{11,18}$)|(^(6[^05])\d{11,18}$)|(^(601)[^1]\d{9,16}$)|(^(6011)\d{9,11}$)|(^(6011)\d{13,16}$)|(^(65)\d{11,13}$)|(^(65)\d{15,18}$)|(^(49030)[2-9](\d{10}$|\d{12,13}$))|(^(49033)[5-9](\d{10}$|\d{12,13}$))|(^(49110)[1-2](\d{10}$|\d{12,13}$))|(^(49117)[4-9](\d{10}$|\d{12,13}$))|(^(49118)[0-2](\d{10}$|\d{12,13}$))|(^(4936)(\d{12}$|\d{14,15}$))/,
        securitycode: /[0-9]{3}/,
        alphanums: /^[a-zA-Z0-9]*$/,
        postcode: /^[ a-zA-Z0-9]*$/,
        words: /^[ a-zA-Z0-9'-]*$/,
        groupid: /^[A-Za-z]{2}[0-9]{4}$/,
        url: /^[0-9a-z\-]+$/i,
        emails: /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/,
        addressline: /^[A-Za-z0-9\-_’'‘ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]+[A-Za-z0-9\-_ ’'‘ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]*[A-Za-z]+[A-Za-z0-9\-_ ’'‘ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]*$/,
        // As addressline but accepts full stop as well
        addressline1: /^[A-Za-z0-9\-_’'‘.ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]+[A-Za-z0-9\-_ ’'‘.ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]*[A-Za-z]+[A-Za-z0-9\-_ ’'‘.ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]*$/,
        firstname: /^[A-Za-z\-_’ '‘.ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]+[A-Za-z\-_’ '‘.ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]*[A-Za-z]+[A-Za-z\-_’ '‘.ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]*$/,
        surname: /^[A-Za-z\-_’ '‘ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]+[A-Za-z\-_’ '‘ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]*[A-Za-z]+[A-Za-z\-_’ '‘ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]*$/
    };

    var scriptPath = function () {
        var scripts = document.getElementsByTagName('SCRIPT');
        var path = '';
        if(scripts && scripts.length>0) {
            for(var i in scripts) {
                if(scripts[i].src && scripts[i].src.match(/\/justgiving\.js$/)) {
                    path = scripts[i].src.replace(/(.*)\/justgiving\.js$/, '$1');
                    break;
                }
            }
        }
        return path;
    };

    var addListeners = function($cont){
        $('input[type=radio]', $cont.display).change(function(){
            if ($(this).attr('id').indexOf("var") > -1)
            {
                if ($(":input[type='number']", $(this).parents('.form-item')).length > 0 )
                {
                    $('input[type=number]', $(this).parents('.form-item')).focus();
                }
                else
                {
                    $('input[type=text]', $(this).parents('.form-item')).focus();    
                }        
            }
            changed(this);
        });
        if ($(":input[type='number']").length > 0 )
        {
            $('input[type=number]', $cont.display).focus(function(){
                inputfocus(this);
            });
        }
        else
        {
            $('input[type=text]', $cont.display).focus(function(){
                inputfocus(this);
            });    
        }
    }

    var androidBrowser = function(){
        var nua = navigator.userAgent
        var isAndroid = (nua.indexOf('Mozilla/5.0') > -1 && 
            nua.indexOf('Android ') > -1 && nua.indexOf('AppleWebKit') > -1 
            && nua.indexOf('Chrome') === -1)
        if (isAndroid) {
            $('select.border').removeClass('border');
        }
    }

    var changed = function($cont){
        var trg;
        $('input[type=radio]', $cont.display).each(function(i){
            if($(this).prop('checked')) trg = this; 
        });
    }

    var inputfocus = function($trg){
        $("input[id*='var'][type=radio]", $($trg).parents('.form-item')).prop('checked', true);
        changed($trg);
    }
    function enableSelectBoxes(){
        $('div.selectBox').each(function(){
            $(this).children('span.selected').html($(this).children('div.selectOptions').children('span.selectOption:first').html());
            $(this).attr('value',$(this).children('div.selectOptions').children('span.selectOption:first').attr('value'));

            $(this).children('span.selected,span.selectArrow').click(function(){
                if($(this).parent().children('div.selectOptions').css('display') == 'none'){
                    $(this).parent().children('div.selectOptions').css('display','block');
                }
                else
                {
                    $(this).parent().children('div.selectOptions').css('display','none');
                }
            });

            $(this).find('span.selectOption').click(function(){
                $(this).parent().css('display','none');
                $(this).closest('div.selectBox').attr('value',$(this).attr('value'));
                $(this).parent().siblings('span.selected').html($(this).html());
            });
        });             
    }
    if (typeof(ga) !== 'undefined') {
        $("form").each(function() {
            $('form :input').blur(function () {
                if($(this).val().length > 0 && !($(this).hasClass('completed'))) {
                    ga('send', 'event', 'Form - ' + $(this).closest('form').attr('name'), 'completed', $(this).attr('name'), 'gaEvent');
                    $(this).addClass('completed')
                }
                else if(!($(this).hasClass('completed')) && !($(this).hasClass('skipped'))) {
                    ga('send', 'event', 'Form - ' + $(this).closest('form').attr('name'), 'skipped', $(this).attr('name'), 'gaEvent');
                    $(this).addClass('skipped');        
                }
            });
        });
    }
    var addValidators = function(){
        $.validator.addMethod("regexp", function(value, element, param) { 
            return this.optional(element) || !param.test(value); 
        });         
        $.validator.addMethod('lettersonly', function(value, element) {
            return this.optional(element) || patterns.letters.test(value);
        });
        $.validator.addMethod('postcode', function(value) {
            return patterns.postcode.test(value);
        });
        $.validator.addMethod('ukpostcode', function(value) {
            return patterns.ukpostcode.test(value);
        });
        $.validator.addMethod('addressline', function(value, element) {
            return (value == '') || patterns.addressline.test(value);
        });
        $.validator.addMethod('addressline1', function(value, element) {
            return (value == '') || patterns.addressline1.test(value);
        });
        $.validator.addMethod('url', function(value, element) {
            return (value == '') || patterns.url.test(value);
        });    
        $.validator.addMethod('firstname', function(value, element) {
            return (value == '') || patterns.firstname.test(value);
        });
        $.validator.addMethod('surname', function(value, element) {
            return (value == '') || patterns.surname.test(value);
        });
        $.validator.addMethod('dateUK', function(value, element) {
            if (Modernizr.inputtypes.date && value.match(/^\d\d\d\d-\d\d?-\d\d?/))
            {
                value = value.split("-").reverse().join("/");
            }
            var check = false,
              re = /^\d{1,2}\/\d{1,2}\/\d{4}$/,
                  adata, gg, mm, aaaa, xdata;
            if ( re.test(value)) {
                adata = value.split("/");
                gg = parseInt(adata[0], 10);
                mm = parseInt(adata[1], 10);
                aaaa = parseInt(adata[2], 10);
                xdata = new Date(aaaa, mm - 1, gg, 12, 0, 0, 0);
                if ( ( xdata.getUTCFullYear() === aaaa ) && ( xdata.getUTCMonth () === mm - 1 ) && ( xdata.getUTCDate() === gg ) ) {
                    check = true;
                } else {
                    check = false;
                }
            } else {
                check = false;
            }
            return check;
        });    
        $.validator.addMethod('ukirelandonly', function(value, element) {
            if ($(element).val() != 'United Kingdom' && $(element).val() != 'Ireland' ) {
                if ($('input[name=createpage]:checked').val() == 1) return false;
                else return true;
            } else {
                return true;
            }
        });
        $.validator.addMethod('ukonly', function(value, element) {
            if ($(element).val() != 'United Kingdom') {
                return false;
            } else {
                return true;
            }
        });
        $.validator.addMethod('telephone', function(value, element) {
            if ($(element).val() != '') {
                return patterns.telephone.test(value);
            } else {
                return true;
            }
        });
        $.validator.addMethod("validateChecked", function (value, element) {
            if ($(element).is(":checked")) return true;
            else return false;
        });  
        if ($("#justgiving").length > 0){
            $("#justgiving").validate({
                focusCleanup: false,
                focusInvalid: true,	
                errorElement: 'span',
                errorContainer: $("span.error"),			
                errorPlacement: function(error, element) {
                    element.parents(".form-item").find("span.error").html(error);
                    $('#justgiving button[type=submit]').removeAttr('disabled');	
                },     	    
                rules: {  
                    'product_amount': {
                        required: true,
                        decimal: true            
                    }              
                },
                messages: { 
                    'product_amount': {
                        required: "Please enter your amount.",
                        decimal: "Please enter a valid amount." 
                    }               
                },        
                submitHandler: function(form) {
                    if ($(form).valid()) {
                        $('#justgiving button[type=submit]').removeAttr('disabled');
                        form.submit();
                    } else {
                        $('#justgiving button[type=submit]').attr('disabled', 'disabled');
                        return false;
                    }                       
                }
            });	        
        }        
        if ($("#login_form").length > 0){
            $("#login_form").validate({
                focusCleanup: false,
                focusInvalid: true,	
                errorElement: 'span',
                errorContainer: $("span.error"),			
                errorPlacement: function(error, element) {
                    //$('.required_text').html(error).addClass('error-text');
                    element.parents(".form-item").find("span.error").html(error);
                    $('#login_form button[type=submit]').removeAttr('disabled');	
                },     	    
                rules: {  
                    'user-name': {
                        required: true,
                        email: true            
                    },
                    password: {
                        required: true          
                    }                
                },
                messages: { 
                    'user-name': {
                        required: "Please enter your email address.",
                        email: "Please enter a valid email address." 
                    }, 
                    password: {
                        required: "Please enter your password."
                    }                
                },        
                submitHandler: function(form) {
                    if ($(form).valid()) {
                        $('#login_form button[type=submit]').removeAttr('disabled');
                        form.submit();
                    } else {
                        $('#login_form button[type=submit]').attr('disabled', 'disabled');
                        return false;
                    }   
                }
            });	
        }    
        if ($("#recover_password").length > 0){
            $("#recover_password").validate({
                focusCleanup: false,
                focusInvalid: true,	
                errorElement: 'span',
                errorContainer: $("span.error"),			
                errorPlacement: function(error, element) {
                    //$('.required_text').html(error).addClass('error-text');
                    element.parents(".form-item").find("span.error").html(error);
                    $('#recover_password button[type=submit]').removeAttr('disabled');	
                },     	    
                rules: {  
                    username_email: {
                        required: true,
                        email: true            
                    }
                },
                messages: { 
                    username_email: {
                        required: "Please enter your email address.",
                        email: "Please enter a valid email address." 
                    },            
                },        
                submitHandler: function(form) {
                    if ($(form).valid()) {
                        $('#recover_password button[type=submit]').removeAttr('disabled');
                        form.submit();
                    } else {
                        $('#recover_password button[type=submit]').attr('disabled', 'disabled');
                        return false;
                    }                         
                }
            });	
        }            
        if ($("#createpage").length > 0){
            if (!Modernizr.inputtypes.date){
                $("#dob").removeAttr('max');
            }
            yepnope({ 
              test : Modernizr.inputtypes.date,
              nope : {
                'css': ajaxsiteroot.url + '/wp-content/plugins/justgiving/js/ui/jquery-ui.min.css',
                'js': ajaxsiteroot.url + '/wp-includes/js/jquery/ui/datepicker.min.js'
              },
              callback: { // executed once files are loaded
                'js': function() { 
                    $( "input[type=date]" ).datepicker({
                        beforeShow: function(input) {
                            $(input).css({
                                "position": "relative",
                                "z-index": 999999
                            });
                        },
                        dateFormat : 'dd/mm/yy',
                        defaultDate: "-1w",
                        showAnim: false,
                        maxDate: "+0d",
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-150:+0"
                    }).focus(function () {
                        $(this).blur()
                    });   
                }}
            });             
            
            $("#createpage").validate({
                ignore: [],
                focusCleanup: false,
                focusInvalid: true,	
                errorElement: 'span',
                errorContainer: $("span.error"),			
                errorPlacement: function(error, element) {
                    //$('.required_text').html(error).addClass('error-text');
                    element.parents(".form-item").find("span.error").html(error);
                    $('#createpage button[type=submit]').removeAttr('disabled');	
                },     	    
                rules: {    
                    pageshortname: {
                        required: true,
                        url: true
                    },
                    pagetitle: {
                        required: true
                    },
                    dob: {
                        required: true
                    },  
                    heardabout: {
                        required: true
                    },
                    work: {
                        maxlength: 9
                    },
                    tsandcs: {
                        required: true,
                        validateChecked: true
                    }
                },
                messages: {
                    pageshortname: {
                        required: "We’re all at sea. You forgot to fill in something.",
                        url: "letters, numbers and hyphens only please"
                    },
                    pagetitle: {
                        required: "We’re all at sea. You forgot to fill in something."
                    },
                    heardabout: {
                        required: "We’re all at sea. You forgot to fill in something."
                    },
                    dob: {
                        required: "We’re all at sea. You forgot to fill in something."
                    },
                    work: {
                        maxlength: "Please limit this field to nine characters."
                    },
                    tsandcs: {
                        required: "Sorry! You must accept the Terms and Conditions",
                        validateChecked: "Sorry! You must accept the Terms and Conditions"
                    }                
                },        
                submitHandler: function(form) {
                    if ($(form).valid()) {
                        $('#createpage button[type=submit]').removeAttr('disabled');
                        form.submit();
                    } else {
                        $('#createpage button[type=submit]').attr('disabled', 'disabled');
                        return false;
                    }                      
                }
            });
            addListeners($("#createpage .pick-amount"));
            $(document).on('change', '#rpageshortname', function() {
              $('#pageshortname').val($( '#rpageshortname' ).val());  
            });        
        }
    
        if ($("#adduser").length > 0){
  
            var submitted = false;
            $("#adduser").validate({
                focusCleanup: false,
                focusInvalid: true,	
                errorElement: 'span',
                errorContainer: $("span.error"),	                
                errorPlacement: function(error, element) {
                    element.parents(".form-item").find("span.error").html(error);
                    $('#adduser button[type=submit]').removeAttr('disabled');	
                },
                invalidHandler: function(form, validator) {
                    $('#adduser button[type=submit]').removeAttr('disabled');
                    submitted = true;
                },           
                rules: {    
                    "title": {
                        required: true
                    },
                    "firstname": {
                        required: true,
                        minlength: 2,
                        firstname: true 
                    },
                    "lastname": {
                        required: true,
                        surname: true
                    },      
                    "address": {
                        required: true,
                        addressline1: true
                    },                
                    "town": {
                        required: true,
                        minlength: 2,
                        lettersonly: true
                    },                
                    "postcode": {
                        required: true,
                        minlength: 2,
                        ukpostcode: true
                    },
                    "email": {
                        required: true,
                        email: true
                    },
                    "country":{
                        required: true,
                        ukonly: true
                    },
                    "password":{
                        required: true,
                        minlength: 6,
                        maxlength: 20
                    },
                    "tsandcs":{
                        required: true,
                        validateChecked: true
                    }
                },
                messages: {
                    "title": {
                        required: "We’re all at sea. You forgot to fill in something."
                    },
                    "email": {
                        required: "We’re all at sea. You forgot to fill in something.",
                        email: "Hmm. There's something wrong with this address. Please check." 
                    }, 
                    "firstname": {
                        required: "We’re all at sea. You forgot to fill in something.",
                        firstname: "Just letters please",
                        minlength: "More than one character please" 
                    },
                    "lastname": {
                        required: "We’re all at sea. You forgot to fill in something.",
                        surname: "Just letters please",
                        minlength: "More than one character please" 
                    },
                    "dob": {
                        required: "We’re all at sea. You forgot to fill in something.",
                        dateUK: "We need this date as dd/mm/yyyy. Thanks!"
                    },                
                    "address": {
                        required: "We’re all at sea. You forgot to fill in something.",
                        addressline1: "Just letters and numbers please."
                    },
                    "address2": {
                        required: "We’re all at sea. You forgot to fill in something.",
                        addressline: "Just letters and numbers please."
                    },                
                    "town": {
                        required: "We’re all at sea. You forgot to fill in something.",
                        minlength: "We’re all at sea. You forgot to fill in something.",
                        lettersonly: "Just letters please"
                    },               
                    "postcode": {
                        required: "We’re all at sea. You forgot to fill in something.",
                        minlength: "We’re all at sea. You forgot to fill in something.",
                        ukpostcode: "Hmm. There's something wrong with this postcode. Please check."
                    },
                    "country":{
                        required: "We’re all at sea. You forgot to fill in something.",
                        ukonly: "Sorry! You must be in the UK to register."
                    },
                    "password":{
                        required: "We’re all at sea. You forgot to fill in something.",
                        minlength: "Just letters and numbers please. 6-20 characters long.",
                        maxlength: "Just letters and numbers please. 6-20 characters long."
                    },
                    "cpassword":{
                        required: "Sorry! You need to fill in this field.",
                        minlength: "Just letters and numbers please. 6-20 characters long.",
                        maxlength: "Just letters and numbers please. 6-20 characters long.",
                        equalTo: "Oops! Those passwords don't match."
                    },
                    "tsandcs":{
                        required: "Sorry! You must accept the terms.",
                        validateChecked: "Sorry! You must accept the terms."
                    }
                },        
                submitHandler: function(form) {
                    if ($(form).valid()) {
                        $('#adduser button[type=submit]').removeAttr('disabled');
                        form.submit();
                    } else {
                        $('#adduser button[type=submit]').attr('disabled', 'disabled');
                        return false;
                    }    
                }
            });	
            $('.optional').hide();
            $('.createaccount').hide();
            var settings = $('#adduser').validate().settings;
            if ($("#country").val() == 'United Kingdom'){
                settings.rules.postcode = {required: true, minlength: 2, ukpostcode: true};
                $("input[name='postcode']").attr('validate', 'required:true'); 
            }
            else if ($("#country").val() == 'Ireland'){
                delete settings.rules.postcode;
                $("input[name='postcode']").removeAttr('validate'); 
            }
            else{
                settings.rules.postcode = {required: true, minlength: 2, postcode: true};
                $("input[name='postcode']").attr('validate', 'required:true');
            }
            if ($("input[name='title']:selected").val() == 'Other'){
                $("input[name='other_title']").show();
            }else{
                $("input[name='other_title']").hide();
            }                
            $('#title').change(function (event) {
                if ($(this).val() == 'Other'){
                    $("input[name='other_title']").show();
                }
                else{
                    $("input[name='other_title']").hide();
                }
            });
            $('#country').change(function (event) {
                if ($(this).val() == 'United Kingdom'){
                    settings.rules.postcode = {required: true, minlength: 2, ukpostcode: true};
                    $("input[name='postcode']").attr('validate', 'required:true');        
                }
                else if ($(this).val() == 'Ireland'){
                    delete settings.rules.postcode;
                    $("input[name='postcode']").removeAttr('validate');        
                }
                else{
                    settings.rules.postcode = {required: true, minlength: 2, postcode: true};
                    $("input[name='postcode']").attr('validate', 'required:true');
                }        
            });       
            $('.optional input').removeAttr('validate');	
        }
    };

    if (Modernizr.inputtypes.date && $( "input[type=date]" ).length > 0){
        var inputDate = $( "input[type=date]" ).val().split('/');
        if ($( "input[type=date]" ).val().indexOf('/') != -1 && inputDate instanceof Array){
            $( "input[type=date]" ).val() = inputDate[2]+'-'+inputDate[1]+'-'+inputDate[0];
        }    
    }   
/*    
    var thistitle;
    $('.hint').hover(
        function(){
            thistitle = $( this ).data('title');
            $( this ).parents('.form-item').find('span.error').html(thistitle);
        },function(){
            if ($( this ).parents('.form-item').find('span.error').html() == $( this ).data('title')){
                $( this ).parents('.form-item').find('span.error').html(' ');
            }
        }
    );
    */
    androidBrowser();
    $(window).load(function(){
        $(document).ready(function(){  
            if ($(".selectBox").length > 0){
                enableSelectBoxes();
            }
        
            if ($("#justgiving").length > 0 ||
                $("#login_form").length > 0 || 
                $("#recover_password").length > 0 || 
                $("#createpage").length > 0 || 
                $("#adduser").length > 0 ){
                if (typeof($.validator) == 'undefined' ) {   
                    //bring in validate as it appears to be missing
                    var pathy = scriptPath();
                    if (pathy.length == 0) pathy = 'wp-content/plugins/justgiving/js';
                    $.ajaxSetup({async:false});
                    $.getScript( pathy+"/jquery.validate.min.js"); 
                    $.getScript( pathy+"/additional-methods.min.js", addValidators );
                    $.ajaxSetup({async:true});
                }
                else addValidators();
            } 
        });
    });
})(jQuery);