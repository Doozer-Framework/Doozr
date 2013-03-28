<?xml version="1.0"?>
<html>
  <head>
    <title tal:content="title">
      Place for the page title
    </title>
  </head>
  <body>
    <h1 tal:content="title">sample title</h1>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Phone</th>
        </tr>
      </thead>
      <tbody>
        <tr tal:repeat="person people">
          <td tal:content="person/name">person's name</td>
          <td tal:content="person/phone">person's phone</td>
        </tr>
        <tr tal:replace="">
          <td>sample name</td>
          <td>sample phone</td>
        </tr>
        <tr tal:replace="">
          <td>sample name</td>
          <td>sample phone</td>
        </tr>
      </tbody>
    </table>
  </body>
</html>