function date_cut_leading_zeros(m) {
  var i;

  if(!m)
    return new Array(0, 0, 0, 0, 0);

  for(i=0; i<m.length; i++) {
    if(typeof m[i]=="string") {
      while((m[i].length>0)&&(m[i].substr(0, 1)=="0"))
        m[i]=m[i].substr(1);
      if(m[i].length==0)
        m[i]="0";
    }
  }

  return m;
}

function date_add_leading_zero(y) {
    y=y+"";
    while(y.length<2)
      y="0"+y;
//  for(i=1;i<3;i++) {
//    y[i]=y[i]+"";
//    if(y[i].length==1)
//      y[i]="0"+y[i];
//  }

  return y;
}

function date_get_today() {
  var d=new Date();

  return date_get_machine(new Array(d.getFullYear(), (d.getMonth()+1), (d.getDate())));
}

function date_get_now(mode) {
  var d=new Date();

  switch(mode) {
    case 1:
    case 3:
      return date_get_machine(new Array(d.getFullYear(), (d.getMonth()+1), (d.getDate())));
    default:
    case 0:
    case 2:
      return date_get_machine(new Array(d.getFullYear(), d.getMonth()+1,
                              d.getDate(), d.getHours(), d.getMinutes()));
  }
}

function date_get_human(date, base, mode) {
  date=date_get_array(date, base, mode);
  if(!date)
    return date;

  if(date.length==3)
    return date[2]+"."+date[1]+"."+date[0];
  else
    return date[2]+"."+date[1]+"."+date[0]+" "+
           date[3]+":"+date_add_leading_zero(date[4]);
}

function date_get_machine(date, base, mode) {
  date=date_get_array(date, base, mode);
  if(!date)
    return date;

  for(i=1;i<date.length;i++) {
    date[i]=date_add_leading_zero(date[i]);
  }

  if(date.length==3)
    return date[0]+"-"+date[1]+"-"+date[2];
  else
    return date[0]+"-"+date[1]+"-"+date[2]+" "+
           date[3]+":"+date[4];
}

function date_get_array(date, base, mode) {
  var m;

  if(!date)
    return null;

  if(typeof date=="object") {
    switch(mode) {
      case 1:
        if(date.length==5)
          return null;
        return date;
      case 2:
        if(date.length==3)
          return null;
        return date;
      case 3:
        return date.slice(0, 3);
      default:
        return date;
    }
  }

  if(m=date.match(/^([0-9][0-9][0-9][0-9])\-([0-9][0-9])\-([0-9][0-9])( ([0-9][0-9]):([0-9][0-9]))?$/)) {
    if((m[4])&&(mode==1))
      return null;
    if((!m[4])&&(mode==2))
      return null;

    m=date_cut_leading_zeros(m);

    if((!m[4])||(mode==3))
      return new Array(m[1], m[2], m[3]);
    else
      return new Array(m[1], m[2], m[3], m[5], m[6]);
  }

  if(m=date.match(/^([0-9][0-9]?)\.([0-9][0-9]?)\.([0-9][0-9][0-9][0-9]|[0-9][0-9])?( ([0-9]?[0-9]):([0-9][0-9]))?$/)) {
    if((m[4])&&(mode==1))
      return null;
    if((!m[4])&&(mode==2))
      return null;

    m=date_cut_leading_zeros(m);
    if(!m[3]) {
      if(!base)
        base=date_get_now();
      base=date_get_last_date(base);
      base=date_get_array(base);
      m[3]=base[0];

      if(!date_is_after(new Array(m[3], m[2], m[1]), base))
        m[3]++;
    }
    else if(m[3]<40) {
      m[3]="20"+m[3];
    }
    else if(m[3]<100) {
      m[3]="19"+m[3];
    }

    if((!m[4])||(mode==3))
      return new Array(m[3], m[2], m[1]);
    else
      return new Array(m[3], m[2], m[1], m[5], m[6]);
  }

  if(m=date.match("^([0-9][0-9]?):([0-9][0-9])$")) {
    m=date_cut_leading_zeros(m);
    if(!base)
      base=date_get_now();
    base=date_get_last_date(base);
    base=date_get_array(base);

    if((m[1]<base[3])||((m[1]==base[3])&&(m[2]<base[4]))) {
      base=date_add(base, 1);
    }

    if(mode==3)
      return new Array(base[0], base[1], base[2]);
    else
      return new Array(base[0], base[1], base[2], m[1], m[2]);
  }


  return null;
}
//alert(date_get_array("4:00"));

function date_is_after(check_date, date) {
  check_date=date_get_machine(check_date);
  date=date_get_machine(date);

  return (check_date>date);
}

function date_get_weekday(date) {
  date=date_get_array(date);
  d=new Date(date[0], date[1]-1, date[2], 12, 0, 0);
  return d.getDay();
}

function date_add(date, diff) {
  var m;
  var n;
  var dl;

  m=false;
  if(typeof date=="string") {
    m=true;
    date=date_get_array(date);
  }

  dl=date.length;
  if(date.length==3) {
    date.push(12);
    date.push(0);
  }

  n=new Date(date[0], date[1]-1, date[2], date[3], date[4], 0).getTime()+86400000*diff;
  n=new Date(n);

  if(dl==3)
    n=new Array(n.getFullYear(), (n.getMonth()+1), n.getDate());
  else
    n=new Array(n.getFullYear(), (n.getMonth()+1), n.getDate(), n.getHours(), n.getMinutes());

  if(m)
    return date_get_machine(n);
  return n;
}

function date_get_js(date) {
  date=date_get_array(date);
  return new Date(date[0], date[1]-1, date[2], date[3], date[4]);
}

function date_diff(date1, date2) {
  d1=date_get_js(date1);
  d2=date_get_js(date2);

  t=d2-d1;
  return date_add_leading_zero(parseInt(t/3600000))+":"+date_add_leading_zero(parseInt(t%3600000/60000));
}

function time_get_human(date, m) {
  date=time_get_array(date, m);
  if(!date)
    return "";

  return date[0]+":"+date_add_leading_zero(date[1]);
}

function time_get_machine(date, m) {
  date=time_get_array(date, m);
  if(!date)
    return "";

  return date_add_leading_zero(date[0])+":"+date_add_leading_zero(date[1]);
}

function time_get_array(date, mon) {
  var m;

  if(typeof date=="object")
    return date;

  if(m=date.match(/^([0-9]+):([0-9][0-9])$/)) {
    return new Array(parseInt(m[1]), parseInt(m[2]));
  }
  if(m=date.match(/^([0-9]+):([0-9][0-9]):[0-9][0-9]$/)) {
    return new Array(parseInt(m[1]), parseInt(m[2]));
  }
  if(m=date.match(/^([0-9]+)[,\.]([0-9])$/)) {
    if(m[2]=="5")
      return new Array(parseInt(m[1]), 30);
    else
      return new Array(parseInt(m[1]),  0);
  }
  if(m=date.match(/^([0-9]+)$/)) {
    return new Array(parseInt(m[1]), 0);
  }

  return null;
}

function date_get_first_date(dates) {
  var i;

  if(!dates)
    return null;
  if(typeof dates=="string")
    return date_get_machine(dates);

  for(i=0; i<dates.length; i++)
    dates[i]=date_get_machine(dates[i]);

  dates.sort();
  return dates[0];
}

function date_get_last_date(dates) {
  var i;

  if(!dates)
    return null;
  if(typeof dates=="string")
    return date_get_machine(dates);

  for(i=0; i<dates.length; i++)
    dates[i]=date_get_machine(dates[i]);

  dates.sort();
  dates.reverse();
  return dates[0];
}
