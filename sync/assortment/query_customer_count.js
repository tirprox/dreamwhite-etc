//on a server
//mongoexport --username admin --password 6h8s4ksoq  --authenticationDatabase admin -d dreamwhite -c temp_results --type=csv -f year,month,count> /tmp/results.csv

db.retaildemand.aggregate([
  {
    $addFields: {
      date: {
        $dateFromString: {
          dateString: "$updated",
          format: "%Y-%m-%d %H:%M:%S",
          onError: '$date',
        },
      },
    },
  },
  {
    $group: {
      _id: {
        agent: "$agent.id",
        "year": {$year: "$date"},
        "month": {$month: "$date"},
      },
      
    },
    
  },
  {
    $project: {
      _id: 0,
      id: "$_id.agent",
      year: "$_id.year",
      month: "$_id.month",
    },
  },
  {
    $group: {
      _id: {
        "month": "$month",
        "year": "$year",
      },
      count: {$sum: 1},
    },
  },
])