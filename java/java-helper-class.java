//Java Helper Class for Janrain Engage

import java.util.Map;

import java.util.HashMap;

import java.util.Iterator;

import java.util.List;

import java.util.ArrayList;

import java.net.URL;

import java.net.URLEncoder;

import java.net.HttpURLConnection;

import java.net.MalformedURLException;

import java.net.ProtocolException;

import java.io.OutputStreamWriter;

import java.io.UnsupportedEncodingException;

import java.io.IOException;

import javax.xml.parsers.DocumentBuilderFactory;

import javax.xml.parsers.DocumentBuilder;

import javax.xml.parsers.ParserConfigurationException;

import javax.xml.xpath.*;

import org.xml.sax.SAXException;

import org.w3c.dom.Document;

import org.w3c.dom.Element;

import org.w3c.dom.NodeList;

public class Rpx {

    private String apiKey;

    private String baseUrl;

    public Rpx(String apiKey, String baseUrl) {

        while (baseUrl.endsWith("/"))

            baseUrl = baseUrl.substring(0, baseUrl.length() - 1);

        this.apiKey = apiKey;

        this.baseUrl = baseUrl;

    }

    public String getApiKey() { return apiKey; }

    public String getBaseUrl() { return baseUrl; }

    public Element authInfo(String token) {

        Map query = new HashMap();

        query.put("token", token);

        return apiCall("auth_info", query);

    }

    public HashMap allMappings() {

        Element rsp = apiCall("all_mappings", null);

        Element mappings_node = (Element)rsp.getFirstChild();

        HashMap result = new HashMap();

        NodeList mappings = getNodeList("/rsp/mappings/mapping", rsp);

        for (int i = 0; i < mappings.getLength(); i++) {

            Element mapping = (Element)mappings.item(i);

            List identifiers = new ArrayList();

            NodeList rk_list = getNodeList("primaryKey", mapping);

            NodeList id_list = getNodeList("identifiers/identifier", mapping);

            String remote_key = ((Element)rk_list.item(0)).getTextContent();

            for (int j = 0; j < id_list.getLength(); j++) {

                Element ident = (Element) id_list.item(j);

                identifiers.add(ident.getTextContent());

            }

            result.put(remote_key, identifiers);

        }

        return result;

    }

    private NodeList getNodeList(String xpath_expr, Element root) {

        XPathFactory factory = XPathFactory.newInstance();

        XPath xpath = factory.newXPath();

        try {

            return (NodeList) xpath.evaluate(xpath_expr, root, XPathConstants.NODESET);

        } catch (XPathExpressionException e) {

            return null;

        }

    }

    public List mappings(Object primaryKey) {

        Map query = new HashMap();

        query.put("primaryKey", primaryKey);

        Element rsp = apiCall("mappings", query);

        Element oids = (Element)rsp.getFirstChild();

        List result = new ArrayList();

        NodeList nl = oids.getChildNodes();

        for (int i = 0; i < nl.getLength(); i++) {

            Element e = (Element)nl.item(i);

            result.add(e.getTextContent());

        }

        return result;

    }

    public void map(String identifier, Object primaryKey) {

        Map query = new HashMap();

        query.put("identifier", identifier);

        query.put("primaryKey", primaryKey);

        apiCall("map", query);

    }

    public void unmap(String identifier, Object primaryKey) {

        Map query = new HashMap();

        query.put("identifier", identifier);

        query.put("primaryKey", primaryKey);

        apiCall("unmap", query);

    }

    private Element apiCall(String methodName, Map partialQuery) {

        Map query = null;

        if (partialQuery == null) {

            query = new HashMap();

        } else {

            query = new HashMap(partialQuery);

        }

        query.put("format", "xml");

        query.put("apiKey", apiKey);

        StringBuffer sb = new StringBuffer();

        for (Iterator it = query.entrySet().iterator(); it.hasNext();) {

            if (sb.length() > 0) sb.append('&');

            try {

                Map.Entry e = (Map.Entry)it.next();

                sb.append(URLEncoder.encode(e.getKey().toString(), "UTF-8"));

                sb.append('=');

                sb.append(URLEncoder.encode(e.getValue().toString(), "UTF-8"));

            } catch (UnsupportedEncodingException e) {

                throw new RuntimeException("Unexpected encoding error", e);

            }

        }

        String data = sb.toString();

        try {

            URL url = new URL(baseUrl + "/api/v2/" + methodName);

            HttpURLConnection conn = (HttpURLConnection)url.openConnection();

            conn.setRequestMethod("POST");

            conn.setDoOutput(true);

            conn.connect();

            OutputStreamWriter osw = new OutputStreamWriter(

                conn.getOutputStream(), "UTF-8");

            osw.write(data);

            osw.close();

            DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();

            dbf.setIgnoringElementContentWhitespace(true);

            DocumentBuilder db = dbf.newDocumentBuilder();

            Document doc = db.parse(conn.getInputStream());

            Element response = (Element)doc.getFirstChild();

            if (!response.getAttribute("stat").equals("ok")) {

                throw new RuntimeException("Unexpected API error");

            }

            return response;

        } catch (MalformedURLException e) {

            throw new RuntimeException("Unexpected URL error", e);

        } catch (IOException e) {

            throw new RuntimeException("Unexpected IO error", e);

        } catch (ParserConfigurationException e) {

            throw new RuntimeException("Unexpected XML error", e);

        } catch (SAXException e) {

            throw new RuntimeException("Unexpected XML error", e);

        }

    }

    public static void main(String [] args) {

        if (args.length < 3) {

            System.out.println("Usage: Rpx <api key> <RPX service URL> <map|unmap|mappings|all_mappings> [...]");

            System.exit(1);

        }

        Rpx r = new Rpx(args[0], args[1]);

        if (args[2].equals("mappings")) {

            System.out.println("Mappings for " + args[3] + ":");

            System.out.println(r.mappings(args[3]));

        }

        if (args[2].equals("map")) {

            System.out.println(args[3] + " mapped to " + args[4]);

            r.map(args[3], args[4]);

        }

        if (args[2].equals("unmap")) {

            System.out.println(args[3] + " unmapped from " + args[4]);

            r.unmap(args[3], args[4]);

        }

        if (args[2].equals("all_mappings")) {

            System.out.println("All mappings:");

            System.out.println(r.allMappings().toString());

        }

    }

}

