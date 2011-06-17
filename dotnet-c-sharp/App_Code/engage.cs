using System;
using System.Collections;
using System.Collections.Generic;
using System.IO;
using System.Net;
using System.Text;
using System.Web;
using System.Xml;
using System.Xml.XPath;


/// <summary>
/// This is a starter class for the Janrain Engage Service.
/// </summary>
public class EngageService
{
    #region class variables


    string baseUrl = "https://rpxnow.com/api/v2/";         //RPXNow.com Auth_info REST web service


    #endregion

    #region class properties

    private string apiKey;
    public string ApiKey
    {
        get { return apiKey; }
        set { apiKey = value; }
    }


    private string firstName;
    public string FirstName
    {
        get { return firstName; }
        set { firstName = value; }
    }


    private string lastName;
    public string LastName
    {
        get { return lastName; }
        set { lastName = value; }
    }

    private string email;
    public string Email
    {
        get { return email; }
        set { email = value; }
    }

    private string identifier;
    public string Identifier
    {
        get { return identifier; }
        set { identifier = value; }
    }

    private string userName;
    public string UserName
    {
        get { return userName; }
        set { userName = value; }
    }

    private string provider;
    public string Provider
    {
        get { return provider; }
        set { provider = value; }
    }
    #endregion

    #region class constructor

    /// <summary>
    /// Constructor with API property being set
    /// </summary>
    /// <param name="ApiKey">This is the Engage API Key</param>
    public EngageService(string ApplicationApiKey)
    {
        this.ApiKey = ApplicationApiKey;
    }

    /// <summary>
    /// Base Constructor
    /// </summary>
    public EngageService() { }


    #endregion

    #region public methods

    /// <summary>
    /// Get the user profile data from the Engage Service
    /// </summary>
    /// <param name="Token">String representing token posted from Engage service</param>
    /// <returns></returns>
    public EngageUser AuthInfo(string Token)
    {
        //build parameters to make the call
        string parameters = "token=" + Token + "&apiKey=" + this.apiKey + "&format=xml";

        //create the URI        
        string uri = baseUrl + "auth_info";

        //make the web request
        WebRequest engageRequest = WebRequest.Create(uri);
        engageRequest.ContentType = "application/x-www-form-urlencoded";
        engageRequest.Method = "POST";
        byte[] bytes = Encoding.ASCII.GetBytes(parameters);
        Stream requestStream = null;

        //Post the request to the auth_info REST web service
        try
        {
            engageRequest.ContentLength = bytes.Length;   //Count bytes to send
            requestStream = engageRequest.GetRequestStream();
            requestStream.Write(bytes, 0, bytes.Length);         //Send it
        }
        catch (WebException ex)
        {
            //Handle WebException error here
            return null;
        }
        finally
        {
            if (requestStream != null)
            {
                requestStream.Close();
            }
        }

        //Get the response from the auth_info REST web service
        try
        {
            //If there's a web response, then read the stream...otherwise exit the flow
            WebResponse engageResponse = engageRequest.GetResponse();
            if (engageResponse == null) { return null; }
            StreamReader engageStream = new StreamReader(engageResponse.GetResponseStream());

            //Create an XML Document and apply our returned payload to the object
            XmlDocument doc = new XmlDocument();
            doc.PreserveWhitespace = false;
            doc.LoadXml(engageStream.ReadToEnd().Trim());
            XmlElement documentElement = doc.DocumentElement;

            //Engage error check.  There are 2 possible conditions, either an empty object OR "stat" not equally OK
            if (documentElement == null || !documentElement.GetAttribute("stat").Equals("ok"))
            {
                // TODO: Handle Unexpected errors
                // throw new Exception("Unexpected API error");
            }

            EngageUser User = new EngageUser(documentElement);
            return User;

        }
        catch (WebException ex)
        {
            //Handle WebException error here
            return null;
        }
    }

    /// <summary>
    /// Get the list of contacts based on Provider
    /// </summary>
    /// <param name="Identifier"></param>
    /// <returns></returns>
    public XmlDocument GetContacts(string Identifier)
    {
        //build parameters to make the call
        string parameters = "identifier=" + Identifier + "&apiKey=" + this.apiKey + "&format=xml";

        //create the URI        
        string uri = baseUrl + "get_contacts";

        //make the web request
        WebRequest engageRequest = WebRequest.Create(uri);
        engageRequest.ContentType = "application/x-www-form-urlencoded";
        engageRequest.Method = "POST";
        byte[] bytes = Encoding.ASCII.GetBytes(parameters);
        Stream requestStream = null;

        //Post the request to the auth_info REST web service
        try
        {
            engageRequest.ContentLength = bytes.Length;   //Count bytes to send
            requestStream = engageRequest.GetRequestStream();
            requestStream.Write(bytes, 0, bytes.Length);         //Send it
        }
        catch (WebException ex)
        {
            //Handle WebException error here
            return null;
        }
        finally
        {
            if (requestStream != null)
            {
                requestStream.Close();
            }
        }

        //Get the response from the auth_info REST web service
        try
        {
            //If there's a web response, then read the stream...otherwise exit the flow
            WebResponse engageResponse = engageRequest.GetResponse();
            if (engageResponse == null) { return null; }
            StreamReader engageStream = new StreamReader(engageResponse.GetResponseStream());

            //Create an XML Document and apply our returned payload to the object
            XmlDocument doc = new XmlDocument();
            doc.PreserveWhitespace = false;
            doc.LoadXml(engageStream.ReadToEnd().Trim());
            XmlElement documentElement = doc.DocumentElement;

            //Engage error check.  There are 2 possible conditions, either an empty object OR "stat" not equally OK
            if (documentElement == null || !documentElement.GetAttribute("stat").Equals("ok"))
            {
                // TODO: Handle Unexpected errors
                // throw new Exception("Unexpected API error");
            }


            return doc;

        }
        catch (WebException ex)
        {
            //Handle WebException error here
            return null;
        }
    }


    /// <summary>
    /// Set the activity of the user on the provider
    /// </summary>
    /// <param name="StatusMessage">JSON packaged string that represents the user activity</param>
    public void SetActivity(string Identifier, string ActivityMessage)
    {
        //build parameters to make the call
        string parameters = "identifier=" + Identifier + "&apiKey=" + this.apiKey + "&format=xml&activity=" + ActivityMessage;


        //create the URI        
        string uri = baseUrl + "activity";

        //make the web request
        WebRequest engageRequest = WebRequest.Create(uri);
        engageRequest.ContentType = "application/x-www-form-urlencoded";
        engageRequest.Method = "POST";
        byte[] bytes = Encoding.ASCII.GetBytes(parameters);
        Stream requestStream = null;

        //Post the request to the auth_info REST web service
        try
        {
            engageRequest.ContentLength = bytes.Length;   //Count bytes to send
            requestStream = engageRequest.GetRequestStream();
            requestStream.Write(bytes, 0, bytes.Length);         //Send it
        }
        catch (WebException ex)
        {
            //Handle WebException error here

        }
        finally
        {
            if (requestStream != null)
            {
                requestStream.Close();
            }
        }

        //Get the response from the auth_info REST web service
        try
        {
            //If there's a web response, then read the stream...otherwise exit the flow
            WebResponse engageResponse = engageRequest.GetResponse();
            if (engageResponse == null) { }
            StreamReader engageStream = new StreamReader(engageResponse.GetResponseStream());

            //Create an XML Document and apply our returned payload to the object
            XmlDocument doc = new XmlDocument();
            doc.PreserveWhitespace = false;
            doc.LoadXml(engageStream.ReadToEnd().Trim());
            XmlElement documentElement = doc.DocumentElement;

            //Engage error check.  There are 2 possible conditions, either an empty object OR "stat" not equally OK
            if (documentElement == null || !documentElement.GetAttribute("stat").Equals("ok"))
            {
                // TODO: Handle Unexpected errors
                // throw new Exception("Unexpected API error");
            }


        }
        catch (WebException ex)
        {
            //Handle WebException error here

        }
    }

}

    #endregion



    /// <summary>
    /// The is a class that represents a user object.  This way
    /// we can take the returned data (in this case XML) and drop
    /// it into object form for use throughout the application
    /// </summary>
    public class EngageUser
    {
        #region class variables




        #endregion

        #region class properties


        private string firstName;
        public string FirstName
        {
            get { return firstName; }
            set { firstName = value; }
        }


        private string lastName;
        public string LastName
        {
            get { return lastName; }
            set { lastName = value; }
        }

        private string email;
        public string Email
        {
            get { return email; }
            set { email = value; }
        }

        private string identifier;
        public string Identifier
        {
            get { return identifier; }
            set { identifier = value; }
        }

        private string userName;
        public string UserName
        {
            get { return userName; }
            set { userName = value; }
        }

        private string provider;
        public string Provider
        {
            get { return provider; }
            set { provider = value; }
        }

        private string photo;
        public string Photo
        {
            get { return photo; }
            set { photo = value; }
        }

        private string providerAccessToken;
        public string ProviderAccessToken
        {
            get { return providerAccessToken; }
            set { providerAccessToken = value; }
        }


        #endregion

        #region class constructor


        /// <summary>
        /// Constructor with XMLElement provided
        /// </summary>
        public EngageUser(XmlElement DocumentElement)
        {
            this.Init(DocumentElement);
        }

        /// <summary>
        /// Base Constructor
        /// </summary>
        public EngageUser() { }

        #endregion

        #region public methods

        /// <summary>
        /// This will populate the user object
        /// </summary>
        /// <param name="Element"></param>
        public void CreateUser(XmlElement Element)
        {
            Init(Element);
        }


        #endregion

        #region private methods

        /// <summary>
        /// This method will populate a user based on the XML input into it
        /// </summary>
        /// <param name="Element">Represents the XML data returned for a user</param>
        private void Init(XmlElement Element)
        {

            //Add values from returned payload to the object properies
            this.Provider = Element.SelectSingleNode("/rsp/profile/providerName").InnerText.ToString();
            this.UserName = Element.SelectSingleNode("/rsp/profile/displayName").InnerText.ToString();
            this.Identifier = Element.SelectSingleNode("/rsp/profile/identifier").InnerText.ToString();
            if (this.Provider == "Google")
            {
                this.Email = Element.SelectSingleNode("/rsp/profile/email").InnerText.ToString();
                this.FirstName = Element.SelectSingleNode("/rsp/profile/name/givenName").InnerText.ToString();
                this.LastName = Element.SelectSingleNode("/rsp/profile/name/familyName").InnerText.ToString();
                this.Photo = null;
            }
            else if (this.Provider == "Yahoo!")
            {
                //Need to split up the "formatted" name to give us FirstName and LastName values
                string[] temp = Element.SelectSingleNode("/rsp/profile/name/formatted").InnerText.ToString().Split(' ');
                this.Email = Element.SelectSingleNode("/rsp/profile/email").InnerText.ToString();
                this.FirstName = temp[0];
                this.LastName = temp[1];
                this.Photo = Element.SelectSingleNode("/rsp/profile/photo").InnerText.ToString();
            }
            else if (this.Provider == "Facebook")
            {
                this.Email = Element.SelectSingleNode("/rsp/profile/email").InnerText.ToString();
                this.FirstName = Element.SelectSingleNode("/rsp/profile/name/givenName").InnerText.ToString();
                this.LastName = Element.SelectSingleNode("/rsp/profile/name/familyName").InnerText.ToString();
                this.Photo = Element.SelectSingleNode("/rsp/profile/photo").InnerText.ToString();
                this.ProviderAccessToken = Element.SelectSingleNode("/rsp/profile/accessCredentials/accessToken").InnerText.ToString();
            }
            else if (this.Provider == "Twitter")
            {
                //Need to split up the "formatted" name to give us FirstName and LastName values
                string[] temp = Element.SelectSingleNode("/rsp/profile/name/formatted").InnerText.ToString().Split(' ');
                this.FirstName = temp[0];
                this.LastName = temp[1];
                this.ProviderAccessToken = Element.SelectSingleNode("/rsp/profile/accessCredentials/oauthToken").InnerText.ToString();
            }
            else if (this.Provider == "LinkedIn")
            {
                //Need to split up the "formatted" name to give us FirstName and LastName values
                string[] temp = Element.SelectSingleNode("/rsp/profile/name/formatted").InnerText.ToString().Split(' ');
                this.FirstName = temp[0];
                this.LastName = temp[1];
                this.Photo = Element.SelectSingleNode("/rsp/profile/photo").InnerText.ToString();

            }
        }

    }

 #endregion