//on a server
//mongoexport --username admin --password 6h8s4ksoq  --authenticationDatabase admin -d dreamwhite -c temp_results --type=csv -f year,month,count> /tmp/results.csv

db.counterparty.aggregate([
  {
    $lookup: {
      from: "counterpartyReport",
      localField: "id",
      foreignField: "counterparty.id",
      as: "report",
    }
  },

])